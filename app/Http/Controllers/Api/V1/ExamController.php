<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Requests\QueryRequest;
use App\Http\Requests\Exam\StoreExamRequest;
use App\Http\Requests\Exam\UpdateExamRequest;
use App\Http\Requests\Practice\GetResultRequest;
use App\Models\Exam;
use App\Models\History;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ExamController extends Controller
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

        $query = Exam::query();
        if ($filterBy && $value) {
            $query->where($filterBy, $condition ?? '=', $value);
        }

        if (!empty($with)) {
            $query->with($with);
        }

        $query->orderBy($sort, $order);
        $exams = $perPage == 0 ? $query->get() : $query->paginate($perPage, ['*'], 'page', $page);

        return Common::response(200, 'Lấy danh sách đề thi thành công', $exams);
    }

    public function store(StoreExamRequest $request)
    {
        $data = $request->validated();
        $data['questions'] = array_unique($data['questions']);
        if (count($data['questions']) != $data['question_count']) {
            return Common::response(400, "Số câu hỏi đã nhập không đúng với số lượng câu hỏi.");
        }
        $data['slug'] = Str::slug($data['name']);
        $data['questions'] = implode(',', $data['questions']);

        $exam = Exam::create($data);

        return $exam
            ? Common::response(201, "Tạo đề thi mới thành công.", $exam)
            : Common::response(400, "Có lỗi xảy ra, vui lòng thử lại.");
    }

    public function update(int $id, UpdateExamRequest $request)
    {
        $exam = Exam::find($id);

        if (!$exam) {
            return Common::response(404, "Không tìm thấy đề thi.");
        }

        $data = $request->validated();

        $data['questions'] = array_unique($data['questions']);
        if (count($data['questions']) != $data['question_count']) {
            return Common::response(400, "Số câu hỏi đã nhập không đúng với số lượng câu hỏi.");
        }
        $data['slug'] = Str::slug($data['name']);
        $data['questions'] = implode(',', $data['questions']);
        Exam::where('id', $id)->update($data);

        $exam = Exam::find($id);
        return Common::response(200, "Cập nhật đề thi thành công", $exam);
    }

    public function destroy(int $id)
    {
        try {
            Exam::destroy($id);
            return Common::response(200, "Xóa đề thi thành công.");
        } catch (\Throwable $th) {
            return Common::response(400, "Có lỗi xảy ra, vui lòng thử lại.");
        }
    }

    public function detail(string $slug)
    {
        $exam = Exam::where('slug', $slug)->first();
        if ($exam) {
            $exam->question_list =  $exam->questions(); // Assuming `questions()` is a relationship method
            $histories = History::where('model', 'App\Models\Exam')->where('foreign_id', $exam->id)->get();
            foreach ($histories as $history) {
                $history->result = json_decode($history->result);
                $history->user = User::find($history->user_id);
            }

            $sortedHistories = $histories->sortByDesc(function ($history) {
                return $history->result->total_score;
            })->values()->all();

            $exam->histories = $sortedHistories;

            return Common::response(200, "Lấy thông tin đề thi thành công.", $exam);
        }

        return Common::response(404, "Không tìm thấy đề thi này.");
    }


    public function result(int $id, GetResultRequest $request)
    {
        $result = [];
        $data = $request->validated();
        $result['time'] = $data['time'];
        $time = $data['time'] / 60;
        $exam = Exam::find($id);
        $questions = $exam->questions();

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
        $result['late'] = ceil($time - $exam->time);

        if ($result['late'] > 0) {
            Common::saveHistory($user_id, 'Exam', $id, $result, "Nộp trễ " . $result['late'] . ' phút.');
            return Common::response(200, 'Bạn đã nộp muộn ' . ($result['late']) . ' phút.', $result);
        }
        Common::saveHistory($user_id, 'Exam', $id, $result);
        return Common::response(200, "Nộp bài thành công!", $result);
    }
}
