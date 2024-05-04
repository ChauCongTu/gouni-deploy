<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Requests\Arena\ProgressRequest;
use App\Http\Requests\QueryRequest;
use App\Http\Requests\Arena\StoreArenaRequest;
use App\Http\Requests\Arena\UpdateArenaRequest;
use App\Http\Requests\Practice\GetResultRequest;
use App\Models\Arena;
use App\Models\Subject;
use App\Models\User;
use Carbon\Carbon;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class ArenaController extends Controller
{
    public function index(QueryRequest $request)
    {
        $with = $request->input('with', []);
        $filterBy = $request->input('filterBy', null);
        $value = $request->input('value', null);
        $condition = $request->input('condition', null);
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 0);
        $sort = $request->input('sort', 'created_at');
        $order = $request->input('order', 'desc');

        $query = Arena::query();
        if ($filterBy && $value) {
            $query->where($filterBy, $condition ?? '=', $value);
        }

        if (!empty($with)) {
            $query->with($with);
        }

        $query->orderBy($sort, $order);
        $arenas = $perPage == 0 ? $query->get() : $query->paginate($perPage, ['*'], 'page', $page);
        foreach ($arenas as $value) {
            $value['author'] = User::find($value['author']);
            $value['users'] = count(explode(',', $value['users']));
            if ($value['subject_id']) {
                $value['subject'] = Subject::find($value['subject_id']);
            }
            $value['is_joined'] = $this->is_joined($value->id, (int) Auth::id());
        }
        return Common::response(200, 'Lấy danh sách phòng thi thành công', $arenas);
    }

    public function store(StoreArenaRequest $request)
    {
        $data = $request->validated();
        $data['questions'] = explode(',', $data['questions']);
        if (count($data['questions']) != $data['question_count']) {
            return Common::response(400, "Số câu hỏi đã nhập không đúng với số lượng câu hỏi.");
        }
        $data['author'] = Auth::id();
        $data['questions'] = implode(',', $data['questions']);
        $arena = Arena::create($data);
        $arena->author = User::find($arena->author);
        $arena->users = count(explode(',', $arena->users));
        if ($arena->subject_id) {
            $arena->subject = Subject::find($arena->subject_id);
        }

        return $arena
            ? Common::response(201, "Tạo phòng thi mới thành công.", $arena)
            : Common::response(400, "Có lỗi xảy ra, vui lòng thử lại.");
    }

    public function update(int $id, UpdateArenaRequest $request)
    {
        $arena = Arena::find($id);

        if (!$arena) {
            return Common::response(404, "Không tìm thấy phòng thi.");
        }

        $validatedData = $request->validated();
        Arena::where('id', $id)->update($validatedData);

        return Common::response(200, "Cập nhật phòng thi thành công", $arena);
    }

    public function destroy(int $id)
    {
        try {
            Arena::destroy($id);
            return Common::response(200, "Xóa phòng thi thành công.");
        } catch (\Throwable $th) {
            return Common::response(400, "Có lỗi xảy ra, vui lòng thử lại.");
        }
    }

    public function detail(int $id)
    {
        $arena = Arena::find($id);
        $user_id = Auth::id();
        if ($arena) {
            $arena['is_joined'] = $this->is_joined($id, $user_id);
            $arena['joined'] = $arena->joined();
            $arena->author = User::find($arena->author);
            $arena['question_list'] = $arena->questions();
            if ($arena['subject_id']) {
                $arena['subject'] = Subject::find($arena['subject_id']);
            }
            return Common::response(200, "Lấy thông tin phòng thi thành công.", $arena);
        }

        return Common::response(404, "Không tìm thấy phòng thi này.");
    }

    public function join(int $id, Request $request)
    {
        $arena = Arena::find($id);

        if ($arena) {
            $password = $request->input('password', null);
            $user_id = Auth::id();
            $joined = $arena->joined();

            if ($arena->status != 'pending') {
                return Common::response(400, 'Phòng thi đã hết thời gian có thể tham gia.');
            }

            if ($this->is_joined($id, $user_id)) {
                return Common::response(400, 'Bạn đã tham gia phòng thi này trước đó.');
            }

            if ($arena->password && $arena->password != $password) {
                return Common::response(400, 'Mật khẩu không chính xác.');
            }

            if ($arena->max_users < count($joined)) {
                return Common::response(400, 'Số lượng người tham gia đã tới giới hạn.');
            }

            $arena['users'] = $arena['users'] . ',' . $user_id;
            $arena->save();

            return Common::response(200, "Tham gia thành công.", $arena);
        }
        return Common::response(404, 'Không tìm thấy phòng thi này.');
    }

    public function leave(int $id)
    {
        $arena = Arena::find($id);

        if (!$arena) {
            return Common::response(404, 'Không tìm thấy phòng thi này.');
        }

        $user_id = Auth::id();

        if (!$this->is_joined($id, $user_id)) {
            return Common::response(400, 'Bạn chưa tham gia phòng thi này.');
        }

        if ($arena->status != 'pending') {
            return Common::response(400, 'Không thể rời phòng thi khi trận đấu đã bắt đầu.');
        }

        $users = explode(',', $arena->users);
        $users = array_diff($users, array($user_id));
        $arena['users'] = implode(',', $users);
        $arena->save();

        return Common::response(200, "Rời phòng thi thành công.");
    }

    public function start(int $id)
    {
        $arena = Arena::find($id);
        if (!$arena) {
            return Common::response(404, 'Không tìm thấy phòng thi này.');
        }
        $user_id = Auth::id();
        if ($arena->author !== $user_id) {
            return Common::response(403, 'Bạn không có quyền bắt đầu trận đấu.');
        }

        if (!$arena) {
            return Common::response(404, 'Không tìm thấy phòng thi này.');
        }

        if ($arena->status != 'pending') {
            return Common::response(400, 'Trạng thái hiện tại không thể bắt đầu.');
        }

        $arena->status = 'started';
        $arena->start_at = now();
        $arena->save();

        return Common::response(200, 'Bắt đầu thi thành công.');
    }

    public function remain(int $id, Request $request)
    {
        $arena = Arena::find($id);

        if (!$arena) {
            return Common::response(404, 'Không tìm thấy phòng thi này.');
        }

        $lastRemainActionAt = Redis::get('arena:' . $id . ':last_remain_action_at');
        $currentTime = now();
        if ($lastRemainActionAt) {
            $lastRemainActionAt = Carbon::createFromTimestamp($lastRemainActionAt);
            $timeDiffInMinutes = $currentTime->diffInMinutes($lastRemainActionAt);
            if ($timeDiffInMinutes < 2) {
                return Common::response(400, 'Chỉ được phép gia hạn thời gian mỗi 2 phút một lần.');
            }
        }

        if ($arena->status !== 'started') {
            return Common::response(400, 'Không thể gia hạn thời gian thi khi trận đấu chưa bắt đầu.');
        }

        $minutesToAdd = $request->input('minute', 5);
        $arena->time += $minutesToAdd;
        Redis::set('arena:' . $id . ':last_remain_action_at', $currentTime->timestamp);
        $arena->save();

        return Common::response(200, 'Gia hạn thời gian thi thành công.');
    }

    public function result(int $id, GetResultRequest $request)
    {
        $result = [];
        $data = $request->validated();
        $result['time'] = $data['time'];
        $time = $data['time'] / 60;
        $arena = Arena::find($id);

        if ($arena->status != 'started') {
            return Common::response(400, 'Trạng thái hiện tại không thể nộp.');
        }

        $questions = $arena->questions();

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
        $result['late'] = ceil($time - $arena->time);

        if ($result['late'] > 0) {
            Common::saveHistory($user_id, 'Arena', $id, $result, "Nộp trễ " . $result['late'] . ' phút.');
            return Common::response(200, 'Bạn đã nộp muộn ' . ($result['late']) . ' phút.', $result);
        }
        Common::saveHistory($user_id, 'Arena', $id, $result);
        return Common::response(200, "Nộp bài thành công!", $result);
    }

    public function saveProgress(ProgressRequest $request)
    {
        Redis::del($request->userId . '_arena_progress_' . $request->arenaId);
        Redis::set($request->userId . '_arena_progress_' . $request->arenaId, $request->progress);
        return Common::response(200, "Lưu thành công!");
    }

    public function loadProgress(ProgressRequest $request)
    {
        $progress = Redis::get(Auth::id() . '_arena_progress_' . $request->arenaId);
        return Common::response(200, "Tải thành công!", $progress);
    }
    public function delProgress(ProgressRequest $request)
    {
        Redis::del(Auth::id() . '_arena_progress_' . $request->arenaId);
        return Common::response(200, "Xóa thành công!");
    }

    public static function is_joined(int $room_id, int $user_id)
    {
        $arena = Arena::find($room_id);
        $joined = $arena->joined();

        $joinedIds = $joined->pluck('id');

        if (in_array($user_id, $joinedIds->toArray())) {
            return true;
        }
        return false;
    }
}
