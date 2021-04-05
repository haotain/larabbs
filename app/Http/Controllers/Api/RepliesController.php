<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Queries\ReplyQuery;
use App\Http\Requests\Api\ReplyRequest;
use App\Http\Resources\ReplyResource;
use App\Models\Reply;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\Request;


class RepliesController extends Controller
{
    /**
     * 发布回复
     */
    public function store(ReplyRequest $request, Topic $topic, Reply $reply)
    {
        $reply->content = $request->content;
        $reply->topic()->associate($topic);
        $reply->user()->associate($request->user());
        $reply->save();

        return new ReplyResource($reply);
    }

    /**
     * 删除回复
     */
    public function destory(Topic $topic, Reply $reply)
    {
        if ($reply->topic_id != $topic->id) {
            abort(404);
        }

        $this->authorize('destroy', $reply);

        $reply->delete();

        return response(null, 204);
    }

    /**
     * 话题回复列表
     */
    public function index(Topic $topic, ReplyQuery $query)
    {
        // $replies = $topic->replies()->paginate();

        $replies = $query->where('topic_id', $topic->id)->paginate();

        return ReplyResource::collection($replies);
    }

    /**
     * 某个用户的回复列表
     */
    public function userIndex($userId, ReplyQuery $query)
    {
        $replies = $query->where('user_id', $userId)->paginate();

        return ReplyResource::collection($replies);
    }
}
