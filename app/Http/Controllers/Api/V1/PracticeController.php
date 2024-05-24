<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\GetResultRequest;
use App\Http\Requests\QueryRequest;
use App\Http\Requests\Practice\StorePracticeRequest;
use App\Http\Requests\Practice\UpdatePracticeRequest;
use App\Models\History;
use App\Models\Practice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PracticeController extends Controller
{
    public function index(QueryRequest $request)
    {
        $with = $request->input('with', []);
        $filterBy = $request->input('filter', null);
        $value = $request->input('value', null);
        $condition = $request->input('condition', null);
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 0);
        $sort = $request->input('sort', 'created_at');
        $order = $request->input('order', 'desc');

        $query = Practice::query();
        if ($filterBy && $value) {
            $query->where($filterBy, $condition ?? '=', $value);
        }

        if (!empty($with)) {
            $query->with($with);
        }

        $query->orderBy($sort, $order);
        $practices = $perPage == 0 ? $query->get() : $query->paginate($perPage, ['*'], 'page', $page);

        return Common::response(200, 'Lấy danh sách bài tập thành công', $practices);
    }

    public function store(StorePracticeRequest $request)
    {
        $data = $request->validated();
        $data['questions'] = array_unique($data['questions']);
        if (count($data['questions']) != $data['question_count']) {
            return Common::response(400, "Số câu hỏi đã nhập không đúng với số lượng câu hỏi.");
        }
        $data['slug'] = Str::slug($data['name']);
        $data['questions'] = implode(',', $data['questions']);

        $practice = Practice::create($data);

        return $practice
            ? Common::response(201, "Tạo bài tập mới thành công.", $practice)
            : Common::response(400, "Có lỗi xảy ra, vui lòng thử lại.");
    }

    public function update(int $id, UpdatePracticeRequest $request)
    {
        $practice = Practice::find($id);

        if (!$practice) {
            return Common::response(404, "Không tìm thấy bài tập.");
        }

        $data = $request->validated();

        $data['questions'] = array_unique($data['questions']);
        if (count($data['questions']) != $data['question_count']) {
            return Common::response(400, "Số câu hỏi đã nhập không đúng với số lượng câu hỏi.");
        }
        $data['slug'] = Str::slug($data['name']);
        $data['questions'] = implode(',', $data['questions']);
        Practice::where('id', $id)->update($data);
        $practice = Practice::find($id);

        return Common::response(200, "Cập nhật bài tập thành công", $practice);
    }

    public function destroy(int $id)
    {
        try {
            Practice::destroy($id);
            return Common::response(200, "Xóa bài tập thành công.");
        } catch (\Throwable $th) {
            return Common::response(400, "Có lỗi xảy ra, vui lòng thử lại.");
        }
    }

    public function detail(string $slug)
    {
        $practice = Practice::where('slug', $slug)->first();
        if ($practice) {
            $practice['question_list'] = $practice->questions();
            $histories = History::where('model', 'App\Models\Practice')->where('foreign_id', $practice->id)->get();
            foreach ($histories as $history) {
                $history->result = json_decode($history->result);
                $history->user = User::find($history->user_id);
            }
            $sortedHistories = $histories->sortByDesc(function ($history) {
                return $history->result->total_score;
            })->values()->all();

            $practice->histories = $sortedHistories;
            return Common::response(200, "Lấy thông tin bài tập thành công.", $practice);
        }
        return Common::response(404, "Không tìm thấy bài tập này.");
    }

    public function result(int $id, GetResultRequest $request)
    {
        $result = [];
        $data = $request->validated();
        $result['time'] = $data['time'];
        $questions = Practice::find($id)->questions();
        $totalQuestions = $questions->count();
        $scorePerQuestion = 10 / $totalQuestions;
        $correct_count = 0;
        $assignment = [];

        foreach ($data['res'] as $key => $value) {
            $question = $questions->find($key);
            $isCorrect = $value == $question->answer_correct;
            $correct_count += $isCorrect ? 1 : 0;
            $score = $isCorrect ? $scorePerQuestion : 0;

            $assignment[$key] = [
                'question' => $question->question,
                'your_answer' => $value,
                'correct_answer' => $question->answer_correct,
                'score' => $score,
            ];
        }

        $total_score = $correct_count * $scorePerQuestion;

        $result['assignment'] = $assignment;

        $result['correct_count'] = $correct_count;
        $result['total_score'] = $total_score;
        $user_id = Auth::id();
        Common::saveHistory($user_id, 'Practice', $id, $result);

        return Common::response(200, "Nộp bài thành công!", $result);
    }
}
