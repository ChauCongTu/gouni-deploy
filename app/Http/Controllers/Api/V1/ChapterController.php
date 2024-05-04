<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Requests\Chapter\QueryRequest;
use App\Http\Requests\Chapter\StoreChapterRequest;
use App\Http\Requests\Chapter\UpdateChapterRequest;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChapterController extends Controller
{
    public function index(QueryRequest $request)
    {
        $relationship = $request->input('getWith', []);
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);
        $sort = $request->input('sort', 'created_at');
        $order = $request->input('order', 'desc');

        $chapterQuery = Chapter::query();
        // dd(count($relationship));
        if (count($relationship) > 0) {
            foreach ($relationship as  $value) {
                $chapterQuery->with($value);
            }
        }

        $chapters = $chapterQuery->orderBy($sort, $order)->paginate($perPage, ['*'], 'page', $page);

        return Common::response(200, 'Lấy danh sách chương thành công', $chapters);
    }

    public function store(StoreChapterRequest $request)
    {
        $newChapter = $request->validated();
        $newChapter['slug'] = Str::slug($newChapter['name']);
        $chapter = Chapter::create($newChapter);

        if ($chapter) {
            return Common::response(201, "Tạo chương mới thành công.", $chapter);
        }

        return Common::response(400, "Có lỗi xảy ra, vui lòng thử lại.");
    }

    public function update(int $id, UpdateChapterRequest $request)
    {
        $chapter = Chapter::find($id);

        if (!$chapter) {
            return Common::response(404, "Không tìm thấy chương.");
        }

        $chapterData = $request->validated();

        if (Chapter::where('name', $chapterData['name'])->where('id', '!=', $id)->doesntExist()) {
            $chapter->name = $chapterData['name'];
            $chapter->slug = Str::slug($chapterData['name']);
            $chapter->content = $chapterData['content'];
            $chapter->save();

            return Common::response(200, "Cập nhật chương thành công", $chapter);
        }

        return Common::response(400, "Có lỗi xảy ra, vui lòng thử lại.");
    }

    public function destroy(int $id)
    {
        try {
            Chapter::destroy($id);
        } catch (\Throwable $th) {
            return Common::response(400, "Có lỗi xảy ra, vui lòng thử lại.");
        }

        return Common::response(200, "Xóa chương thành công.");
    }

    public function detail(string $slug)
    {
        $chapter = Chapter::where('slug', $slug)->first();

        if ($chapter) {
            return Common::response(200, "Lấy thông tin chương thành công.", $chapter);
        }

        return Common::response(404, "Không tìm thấy chương này.");
    }
}
