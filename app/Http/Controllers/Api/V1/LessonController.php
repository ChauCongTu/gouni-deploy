<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Requests\Lesson\StoreLessonRequest;
use App\Http\Requests\Lesson\UpdateLessonRequest;
use App\Http\Requests\QueryRequest;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LessonController extends Controller
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

        $query = Lesson::query();
        if ($filterBy && $value) {
            $query = ($condition) ? $query->where($filterBy, $condition, $value) : $query->where($filterBy, $value);
        }

        if (count($with) > 0) {
            $query->with($with);
        }

        $query = $query->orderBy($sort, $order);
        if ($perPage == 0) {
            $lessons = $query->get();
        } else {
            $lessons = $query->paginate($perPage, ['*'], 'page', $page);
        }

        foreach ($lessons as $lesson) {
            $lesson['liked_list'] = $lesson->likeLists();
        }

        return Common::response(200, 'Lấy danh sách bài học thành công', $lessons);
    }

    public function store(StoreLessonRequest $request)
    {
        $newLesson = $request->validated();
        $newLesson['slug'] = Str::slug($newLesson['name']);
        $lesson = Lesson::create($newLesson);

        if ($lesson) {
            return Common::response(201, "Tạo bài học mới thành công.", $lesson);
        }

        return Common::response(404, "Có lỗi xảy ra, vui lòng thử lại.");
    }

    public function handleLike(int $id)
    {
        $user_id = Auth::id();
        $lesson = Lesson::find($id);
        if ($lesson) {
            $likeList = explode(',', $lesson->likes);
            if (in_array($user_id, $likeList)) {
                $likeList = array_diff($likeList, array($user_id));
                $lesson->likes = implode(',', $likeList);
                $lesson->save();
                return Common::response(200, "Bỏ thích bài viết thành công.", $lesson->likeLists(), null, 'like', false);
            }
            $likeList[] = $user_id;
            $lesson->likes = implode(',', $likeList);
            $lesson->save();
            return Common::response(200, "Thích bài viết thành công.", $lesson->likeLists(), null, 'like', true);
        }
        return Common::response(404, "Có lỗi xảy ra, vui lòng thử lại.");
    }

    public function update(int $id, UpdateLessonRequest $request)
    {
        $lesson = Lesson::find($id);

        if (!$lesson) {
            return Common::response(404, "Không tìm thấy bài học.");
        }

        $lessonData = $request->validated();

        if (isset($lessonData['title'])) {
            $lessonData['slug'] = Str::slug($lessonData['title']);
        }

        $lesson->update($lessonData);

        return Common::response(200, "Cập nhật bài học thành công", $lesson);
    }

    public function destroy(int $id)
    {
        try {
            Lesson::destroy($id);
        } catch (\Throwable $th) {
            return Common::response(400, "Có lỗi xảy ra, vui lòng thử lại.");
        }

        return Common::response(200, "Xóa bài học thành công.");
    }

    public function detail(string $slug)
    {
        $lesson = Lesson::where('slug', $slug)->first();
        $lesson['liked_list'] = $lesson->likeLists();

        if ($lesson) {
            return Common::response(200, "Lấy thông tin bài học thành công.", $lesson);
        }

        return Common::response(404, "Không tìm thấy bài học này.");
    }
}
