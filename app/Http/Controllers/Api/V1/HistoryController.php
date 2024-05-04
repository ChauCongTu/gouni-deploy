<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Models\History;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 0);
        $query = History::where('user_id', Auth::id())->orderBy('created_at', 'DESC');

        if ($perPage === 0) {
            $histories = $query->get();
        } else {
            $histories = $query->paginate($perPage);
        }
        foreach ($histories as $value) {
            if ($value['model'] == 'App\Models\Exam') {
                $value['type'] = 'Exam';
            } else if ($value['model'] == 'App\Models\Arena') {
                $value['type'] = 'Arena';
            } else if ($value['model'] == 'App\Models\Practice') {
                $value['type'] = 'Practice';
            }
            $value['model'] = $value['model']::where('id', $value['foreign_id'])->first();
            $value['result'] = json_decode($value['result']);
        }
        return Common::response(200, "Lấy danh sách lịch sử thành công.", $histories);
    }

    public function element(int $id)
    {
        $history = History::find($id);
        if ($history->model == 'App\Models\Exam') {
            $history->type = 'Exam';
        } else if ($history->model == 'App\Models\Arena') {
            $history->type = 'Arena';
        } else if ($history->model == 'App\Models\Practice') {
            $history->type = 'Practice';
        }
        $history->model = $history->model::where('id', $history->foreign_id)->first();
        $history->result = json_decode($history->result);
        return Common::response(200, "Lấy lịch sử thành công.", $history);
    }

    public function detail(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());

        $histories = History::where('user_id', Auth::id())
            ->whereDate('created_at', $date)
            ->get();
        foreach ($histories as $value) {
            $value['result'] = json_decode($value['result']);
        }
        return Common::response(200, "Lấy danh sách lịch sử của ngày $date thành công.", $histories);
    }
}
