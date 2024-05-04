<?php

use App\Helpers\Common;
use App\Models\History;
use App\Models\Statistic;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

Route::get('/', function () {
    $message = 'Bạn không có quyền truy cập vào nguồn dữ liệu đã yêu cầu.';
    return Common::response(403, $message);
});
Route::get('/response-403', function () {
    $message = 'Bạn không có quyền truy cập vào nguồn dữ liệu đã yêu cầu.';
    return Common::response(403, $message);
})->name('login');

Route::get('/403', function () {
    return Response::HTTP_FORBIDDEN;
})->name('403');

Route::get('/statictis', function () {
    $users = User::get();
    $user_stats = new Statistic();
    foreach ($users as $user) {
        $user_stats = new Statistic();
        $userHistories = [];
        $user_stats->user_id = $user->id;
        $histories = History::todayByUser($user->id)->get();
        if (count($histories) == 0) {
            $user_stats->save();
            continue;
        }
        $user_stats->min_score = 10;
        $user_stats->max_score = 0;
        $user_stats->avg_score = 0;
        $user_stats->total_questions_done = 0;
        $correct_question = 0;
        $doToday = [];
        foreach ($histories as $history) {
            $result = json_decode($history['result']);
            // Tính tổng thời gian làm bài trong ngày (giây)
            $user_stats->total_time += $result->time;
            // - Số lượng bài đề giải trong ngày.
            if ($history->model == 'App\Models\Exam') {
                $user_stats->total_exams++;
            }
            if ($history->model == 'App\Models\Practice') {
                $user_stats->total_practices++;
            }
            if ($history->model == 'App\Models\Arena') {
                $user_stats->total_arenas++;
            }

            // - Danh sách bài tập đã làm trong ngày (json): list id của History
            $userHistories[] = $result;
            // - Min score
            if ($user_stats->min_score > $result->total_score) {
                $user_stats->min_score = $result->total_score;
            }
            // Tính max score
            if ($user_stats->max_score < $result->total_score) {
                $user_stats->max_score = $result->total_score;
            }

            // Tính avg score
            $user_stats->avg_score += $result->total_score;
            // - Số lần nộp bài muộn
            if ($result->late > 0) {
                $user_stats->late_submissions++;
            }
            // - Các môn thi đã làm hôm nay: json_decode subject_id  =>  { lần => điểm }
            if ($history->model == 'App\Models\Exam') {
                $exam = $history->model::find($history->foreign_id);

                $late = $result->late > 0 ? true : false;

                $doToday[$exam->subject_id][] = [
                    'score' => $result->total_score,
                    'exam' => $history->foreign_id,
                    'time' => $result->time,
                    'late' => $late
                ];
            }

            // - Tổng số câu làm.
            $user_stats->total_questions_done += count((array)$result->assignment);

            // - Tỷ lệ làm đúng
            $correct_question += $result->correct_count;
        }
        // - Môn thi làm nhiều nhất
        $counted = collect($doToday)->map(function ($item, $key) {
            return count($item);
        });

        $user_stats->most_done_subject = $counted->keys()->max(function ($key) use ($counted) {
            return $counted[$key];
        });
        // dd($doToday);
        $user_stats->histories = json_encode($userHistories);
        $user_stats->subjects_done_today = json_encode($doToday);
        $user_stats->avg_score = $user_stats->avg_score / count($histories);
        $user_stats->accuracy = ceil($correct_question / $user_stats->total_questions_done * 100);
        $user_stats->day_stats = date('Y-m-d H:i:s', time());
        $user_stats->save();
    }
});
