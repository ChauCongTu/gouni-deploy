<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTargetRequest;
use App\Http\Requests\UpdateTargetRequest;
use App\Models\History;
use Illuminate\Http\Request;
use App\Models\UserTarget;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TargetController extends Controller
{
    public function firstOfDay()
    {
        $user_id = Auth::id();
        if ($this->is_first($user_id)) {
            return Common::response(400, "Bạn đã đặt target cho ngày hôm nay rồi.");
        }
        return Common::response(200, 'Bạn có thể đặt target ngay lúc này.');
    }
    public function store(StoreTargetRequest $request)
    {
        $user_id = Auth::id();
        if ($this->is_first($user_id)) {
            return Common::response(400, "Bạn đã đặt target cho ngày hôm nay rồi.");
        }
        $validatedData = $request->validated();
        $validatedData['user_id'] = $user_id;
        $validatedData['total_time'] = $validatedData['total_time'] * 60;
        $validatedData['day_targets'] = Carbon::today()->toDateString();

        $target = UserTarget::create($validatedData);

        $target['total_time'] = $target['total_time'] / 60;

        return $target ?
            Common::response(201, "Tạo mục tiêu mới thành công.", $target) :
            Common::response(400, "Có lỗi xảy ra, vui lòng thử lại.");
    }

    public function update(UpdateTargetRequest $request, int $id)
    {
        $user_id = Auth::id();
        $validatedData = $request->validated();
        $validatedData['user_id'] = Auth::id();

        $target = UserTarget::find($id);

        $validatedData['user_id'] = $user_id;
        $validatedData['total_time'] = $validatedData['total_time'] * 60;
        $validatedData['day_targets'] = Carbon::today()->toDateString();

        if (!$target) {
            return Common::response(404, "Không tìm thấy mục tiêu.");
        }

        $target->update($validatedData);

        return Common::response(200, "Cập nhật mục tiêu thành công", $target);
    }

    public function destroy(int $id)
    {
        $target = UserTarget::find($id);

        if (!$target) {
            return Common::response(404, "Không tìm thấy mục tiêu.");
        }

        $target->delete();

        return Common::response(200, "Xóa mục tiêu thành công.");
    }

    public function index()
    {
        $targets = UserTarget::where('user_id', Auth::id())->orderBy('day_targets', 'DESC')->get();
        return Common::response(200, "Lấy danh sách target thành công.", $targets);
    }

    public function detail($date = null)
    {
        $date = $date ?? Carbon::today()->toDateString();
        $target = UserTarget::where('user_id', Auth::id())
            ->where('day_targets', $date)
            ->first();

        if ($target) {
            return Common::response(200, "Lấy danh sách target thành công.", $target);
        }
        return Common::response(404, "Không tìm thấy target.");
    }

    public function reality($date = null)
    {
        $date = $date ?? Carbon::today()->toDateString();

        $data = [];
        $data['total_exams'] = History::where('user_id', Auth::id())->where('model', 'App\Models\Exam')->where('created_at', 'LIKE', $date . '%')->count();
        $data['total_practices'] = History::where('user_id', Auth::id())->where('model', 'App\Models\Practice')->where('created_at', 'LIKE', $date . '%')->count();
        $data['total_arenas'] = History::where('user_id', Auth::id())->where('model', 'App\Models\Arena')->where('created_at', 'LIKE', $date . '%')->count();
        $data['min_score'] = 0;
        $data['accuracy'] = 0;
        $histories = History::where('user_id', Auth::id())->where('created_at', 'LIKE', $date . '%')->get();
        if ($histories->count() > 0) {
            $data['min_score'] = 10;
            $data['accuracy'] = 0;
            foreach ($histories as $history) {
                $history['result'] = json_decode($history['result']);
                if (is_array($history['result'])) {
                    if ($history['result']['total_score'] < $data['min_score']) {
                        $data['min_score'] = $history['result']['total_score'];
                    }
                    $accuracy = $history['result']['correct_count'] / count($history['result']['assignment']) * 100;
                    $data['accuracy'] += $accuracy;
                }
            }
            $data['accuracy'] = $data['accuracy'] / $histories->count();
        }

        if ($data) {
            return Common::response(200, "Lấy danh sách target thành công.", $data);
        }
        return Common::response(404, "Không tìm thấy target.");
    }

    public static function is_first($user_id, $date = null)
    {
        $date = $date ?? Carbon::today()->toDateString();

        $exists = UserTarget::where('user_id', $user_id)
            ->where('day_targets', $date)
            ->exists();

        return $exists;
    }
}
