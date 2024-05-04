<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTargetRequest;
use App\Http\Requests\UpdateTargetRequest;
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

    public static function is_first($user_id, $date = null)
    {
        $date = $date ?? Carbon::today()->toDateString();

        $exists = UserTarget::where('user_id', $user_id)
            ->where('day_targets', $date)
            ->exists();

        return $exists;
    }
}
