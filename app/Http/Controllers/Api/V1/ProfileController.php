<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangeAvatarRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Requests\QueryRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ProfileController extends Controller
{
    public function update(UpdateProfileRequest $request)
    {
        $user_id = Auth::id();
        $profile = $request->validated();
        $profile['test_class'] = implode(',', $profile['test_class']);
        User::where('id', $user_id)->update($profile);
        $user = User::find(1);
        return Common::response(200, "Thành công", $user);
    }

    public function avatar(ChangeAvatarRequest $request)
    {
        $id = Auth::id();
        $user = User::find($id);

        $avatar = $request->file('avatar');
        $avatarName = $id . '.jpg';
        $avatarPath = 'public/avatar/' . $avatarName;

        $existingAvatarPath = $user->avatar;
        if ($existingAvatarPath) {
            Storage::delete(str_replace(env('APP_URL') . '/storage/', 'public/', $existingAvatarPath));
        }

        // Lưu ảnh mới vào thư mục storage
        Storage::putFileAs('public/avatar/', $avatar, $avatarName);

        $user->avatar = env('APP_URL') . '/storage/avatar/' . $avatarName;
        $user->save();

        // Trả về response thành công
        return Common::response(200, 'Avatar changed successfully', ['avatar' => $user->avatar]);
    }

    public function list(QueryRequest $request)
    {
        $with = $request->input('with', []);
        $filterBy = $request->input('filter', null);
        $value = $request->input('value', null);
        $condition = $request->input('condition', null);
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 0);
        $sort = $request->input('sort', 'created_at');
        $order = $request->input('order', 'desc');

        $query = User::query();
        if ($filterBy && $value) {
            $query = ($condition) ? $query->where($filterBy, $condition, $value) : $query->where($filterBy, $value);
        }

        if (count($with) > 0) {
            $query->with($with);
        }

        $query = $query->orderBy($sort, $order);
        if ($perPage == 0) {
            $users = $query->get();
        } else {
            $users = $query->paginate($perPage, ['*'], 'page', $page);
        }

        return Common::response(200, 'Lấy danh sách người dùng thành công', $users);
    }
    public function index(string $username = null)
    {
        $user = User::where('username', $username)->first();
        $user['test_class'] = explode(',', $user['test_class']);
        if ($user) {
            return Common::response(200, 'Lấy thông tin người dùng thành công.', $user);
        }
        return Common::response(404, 'Không tìm thấy người dùng có username là ' . $username);
    }
}
