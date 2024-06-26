<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Requests\Topic\StoreTopicRequest;
use App\Http\Requests\Topic\UpdateTopicRequest;
use App\Http\Requests\QueryRequest;
use App\Http\Requests\Topic\CommentRequest;
use App\Http\Requests\Topic\UpdateCommentRequest;
use App\Models\Topic;
use App\Models\TopicComment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TopicController extends Controller
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

        $query = Topic::query();
        if ($filterBy && $value) {
            $query = ($condition) ? $query->where($filterBy, $condition, $value) : $query->where($filterBy, $value);
        }

        if (count($with) > 0) {
            $query->with($with);
        }

        $query = $query->orderBy($sort, $order);
        if ($perPage == 0) {
            $topics = $query->get();
        } else {
            $topics = $query->paginate($perPage, ['*'], 'page', $page);
        }
        foreach ($topics as $topic) {
            $topic['author']  = User::find($topic['author']);
        }

        return Common::response(200, 'Lấy danh sách chủ đề thành công', $topics);
    }

    public function store(StoreTopicRequest $request)
    {
        $newTopic = $request->validated();
        $newTopic['slug'] = Str::slug($newTopic['title']);
        $newTopic['author'] = Auth::id();
        $topic = Topic::create($newTopic);

        if ($topic) {
            return Common::response(201, "Tạo chủ đề mới thành công.", $topic);
        }

        return Common::response(404, "Có lỗi xảy ra, vui lòng thử lại.");
    }

    public function update(int $id, UpdateTopicRequest $request)
    {
        $topic = Topic::find($id);

        if (!$topic) {
            return Common::response(404, "Không tìm thấy chủ đề.");
        }

        $topicData = $request->validated();

        if (isset($topicData['title'])) {
            $topicData['slug'] = Str::slug($topicData['title']);
        }

        $topic->update($topicData);

        $topic = Topic::with('comments')->where('id', $id)->first();
        if ($topic) {
            $topic->author = User::find($topic->author);
            $comments = $topic->comments;
            foreach ($comments as $value) {
                $userList = explode(',', $value->likes);
                $userList = User::select('name', 'username')->whereIn('id', $userList)->get();
                $value['liked_list'] = $userList;
                $value['author'] = User::find($value->author);
            }
        }

        return Common::response(200, "Cập nhật chủ đề thành công", $topic);
    }

    public function destroy(int $id)
    {
        try {
            Topic::destroy($id);
        } catch (\Throwable $th) {
            return Common::response(400, "Có lỗi xảy ra, vui lòng thử lại.");
        }

        return Common::response(200, "Xóa chủ đề thành công.");
    }

    public function detail(string $slug)
    {
        $topic = Topic::with('comments')->where('slug', $slug)->first();
        if ($topic) {
            $topic->author = User::find($topic->author);
            $comments = $topic->comments;
            foreach ($comments as $value) {
                $userList = explode(',', $value->likes);
                $userList = User::select('name', 'username')->whereIn('id', $userList)->get();
                $value['liked_list'] = $userList;
                $value['author'] = User::find($value->author);
            }
            return Common::response(200, "Lấy thông tin chủ đề thành công.", $topic);
        }

        return Common::response(404, "Không tìm thấy chủ đề này.");
    }



    public function comment(int $id)
    {
        $comments = TopicComment::where('topic_id', $id)->paginate(10);

        foreach ($comments as $comment) {
            $comment->likes_count = count(explode(',', $comment->likes));
        }

        $sorted_comments = $comments->sortByDesc('likes_count');

        return Common::response(200, "Lấy danh sách bình luận thành công.", $sorted_comments);
    }

    public function postComment(int $topic_id, CommentRequest $request)
    {
        $comment = $request->validated();
        $comment['author'] = Auth::id();

        $newComment = TopicComment::create($comment);

        if ($newComment) {
            $newComment->author = User::find($newComment->author);
            $newComment['liked_list'] = [];
            return Common::response(200, "Bình luận thành công.", $newComment);
        }
        return Common::response(400, "Bình luận không thành công.");
    }
    public function updateComment(int $topic_id, int $id, UpdateCommentRequest $request)
    {
        $comment = TopicComment::find($id);
        if ($comment) {
            if ($comment->author != Auth::id()) {
                return Common::response(403, "Bạn không có quyền sửa bình luận này.");
            }
            $comment->content = $request->content;
            $comment->attachment = $request->attachment;
            $comment->save();
            return Common::response(200, "Chỉnh sửa bình luận thành công.", $comment);
        }
        return Common::response(400, "Bình luận không thành công.");
    }
    public function destroyComment(int $id)
    {
        try {
            $comment = TopicComment::destroy($id);
            // $comment->delete();
        } catch (\Exception $e) {
            return Common::response(400, "Có lỗi xảy ra, vui lòng thử lại.");
        }

        return Common::response(200, "Xóa bình luận thành công.");
    }

    public function likeComment(int $topic_id, int $id)
    {
        $user_id = Auth::id();
        $comment = TopicComment::find($id);
        if ($comment) {
            $likeList = explode(',', $comment->likes);
            if (in_array($user_id, $likeList)) {
                $likeList = array_diff($likeList, array((string)$user_id));
                $comment->likes = implode(',', $likeList);
                $comment->save();
                return Common::response(200, "Bỏ thích bài viết thành công.", $comment->likeLists(), null, 'like', false);
            }
            $likeList[] = $user_id;
            $comment->likes = implode(',', $likeList);
            $comment->save();
            return Common::response(200, "Thích bài viết thành công.", $comment->likeLists(), null, 'like', true);
        }
        return Common::response(404, "Có lỗi xảy ra, vui lòng thử lại.");
    }
}
