<?php
namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Models\Statistic;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatisticController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->input('type', 'd');
        $numb = $request->input('numb', 7);
        $userId = Auth::id();
        $result = [];

        $intervalMapping = [
            'd' => 'day',
            'w' => 'week',
            'm' => 'month',
            'y' => 'year'
        ];

        $inputDate = $request->input('date');
        $date = $inputDate ? Carbon::parse($inputDate) : Carbon::today();

        for ($i = 0; $i < $numb; $i++) {
            $interval = $intervalMapping[$type];
            $startDate = $date->copy()->{"sub{$interval}s"}($i)->startOf($interval);
            $endDate = $date->copy()->{"sub{$interval}s"}($i)->endOf($interval);
            $intervalNumber = $startDate->{$interval};

            $stats = Statistic::where('user_id', $userId)
                ->whereBetween('day_stats', [$startDate, $endDate])
                ->get();

            $stat = $this->calculateStats($stats);

            $result[] = [
                'interval' => $intervalNumber,
                'type' => $intervalMapping[$type],
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'stats' => $stat
            ];
        }

        return Common::response(200, "Lấy thống kê thành công.", $result);
    }

    private function calculateStats($stats)
    {
        $stat = [
            "total_time" => 0,
            "total_exams" => 0,
            "total_practices" => 0,
            "total_arenas" => 0,
            "histories" => [],
            "min_score" => 10,
            "max_score" => 0,
            "avg_score" => 0,
            "late_submissions" => 0,
            "accuracy" => 0,
            "most_done_subject" => 0,
            "subjects_done_today" => [],
            "total_questions_done" => 0
        ];

        if ($stats->isNotEmpty()) {
            foreach ($stats as $value) {
                $value->histories = json_decode($value->histories);
                $value->subjects_done_today = json_decode($value->subjects_done_today);
                $stat['total_time'] += $value->total_time;
                $stat['total_exams'] += $value->total_exams;
                $stat['total_practices'] += $value->total_practices;
                $stat['total_arenas'] += $value->total_arenas;
                $stat['min_score'] = min($stat['min_score'], $value->min_score);
                $stat['max_score'] = max($stat['max_score'], $value->max_score);
                $stat['avg_score'] += $value->avg_score;
                $stat['accuracy'] += $value->accuracy;
                $stat['late_submissions'] += $value->late_submissions;
                $stat['histories'] = array_merge($stat['histories'], (array)$value->histories);
                $stat['subjects_done_today'] = array_merge($stat['subjects_done_today'], (array)$value->subjects_done_today);
                $stat['total_questions_done'] += $value->total_questions_done;
            }
            $stat['avg_score'] = ceil($stat['avg_score'] / count($stats));
            $stat['accuracy'] = ceil($stat['accuracy'] / count($stats));
        }

        if ($stats->isEmpty()) {
            $stat['min_score'] = 0;
        }

        return $stat;
    }
}
