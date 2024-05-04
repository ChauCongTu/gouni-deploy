<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Requests\Subject\QueryRequest;
use App\Http\Requests\Subject\StoreSubjectRequest;
use App\Http\Requests\Subject\UpdateSubjectRequest;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubjectController extends Controller
{
    public function index(QueryRequest $request)
    {
        $relationship = $request->input('getWith', []);
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);
        $sort = $request->input('sort', 'created_at');
        $order = $request->input('order', 'desc');
        $grade = $request->input('grade', null);

        $query = Subject::query();
        // dd(count($relationship));
        if (count($relationship) > 0) {
            foreach ($relationship as  $value) {
                $query = $query->with($value);
            }
        }
        if ($grade) {
            $query = $query->where('grade', $grade);
        }
        // Query users
        $subjects = $query->orderBy($sort, $order)->paginate($perPage, ['*'], 'page', $page);

        return Common::response(200, 'Lấy danh sách môn học thành công', $subjects);
    }
    public function store(StoreSubjectRequest $request)
    {
        $newSubject = $request->validated();
        $newSubject['slug'] = Str::slug($newSubject['name']);
        $subject = Subject::create($newSubject);
        if ($subject) {
            return Common::response(201, "Tạo môn học mới thành công.", $subject);
        }
        return Common::response(400, "Có lỗi xảy ra, vui lòng thử lại.");
    }
    public function update(int $id, UpdateSubjectRequest $request)
    {
        $subject = Subject::find($id);
        if (Subject::where('name', $request->name)->where('id', '!=', $id)->doesntExist()) {
            $subject->name = $request->name;
            $subject->slug = Str::slug($request->name);
            $subject->grade = $request->grade;
            $subject->save();
            return Common::response(200, "Cập nhật môn học thành công", $subject);
        }
        return Common::response(400, "Có lỗi xảy ra, vui lòng thử lại.");
    }
    public function destroy(int $id)
    {
        try {
            Subject::destroy($id);
        } catch (\Throwable $th) {
            return Common::response(400, "Có lỗi xảy ra, vui lòng thử lại.");
        }
        return Common::response(200, "Xóa môn học thành công.");
    }
    public function detail(string $slug)
    {
        $subject = Subject::with('chapters')->where('slug', $slug)->first();
        if ($subject) {
            return Common::response(200, "Lấy thông tin môn học thành công.", $subject);
        }
        return Common::response(404, "Không tìm thấy môn học này.");
    }
}
