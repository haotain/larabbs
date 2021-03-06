<?php

namespace App\Http\Controllers\Api;

use App\Http\Queries\TopicQuery;
use App\Http\Requests\Api\TopicRequest;
use App\Http\Resources\TopicResource;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class TopicsController extends Controller
{
    /**
     * 发布话题
     */
    public function store(TopicRequest $request, Topic $topic)
    {
        $topic->fill($request->all());
        $topic->user_id = $request->user()->id;
        $topic->save();

        return new TopicResource($topic);
    }

    /**
     * 更新话题
     */
    public function update(TopicRequest $request, Topic $topic)
    {
        $this->authorize('update', $topic);

        $topic->update($request->all());

        return new TopicResource($topic);
    }

    /**
     * 删除话题
     */
    public function destroy(Topic $topic)
    {
        $this->authorize('destroy', $topic);

        $topic->delete();

        return response(null, 204);
    }

    /**
     * 话题列表
     */
    public function index(Request $request, Topic $topic, TopicQuery $query)
    {

        $topics = $query->paginate();

        // $query = $topic->query();

        // if ($categoryId = $request->category_id) {
        //     $query->where('category_id', $categoryId);
        // }

        // $topics = $query->with('user', 'category')->withOrder($request->order)->paginate();

        return TopicResource::collection($topics);
    }

    /**
     * 某用户发布的话题
     */
    public function userIndex(Request $request, User $user, TopicQuery $query)
    {

        $topics = $query->where('user_id', $user->id)->paginate();

        // TopicQuery 累来替换下面代码
        // $query = $user->topics()->getQuery();

        // $topics = QueryBuilder::for($query)
        //     ->allowedIncludes('user', 'category')
        //     ->allowedFilters([
        //         'title',
        //         AllowedFilter::exact('category_id'),
        //         AllowedFilter::scope('withOrder')->default('recentReplied')
        //     ])
        //     ->paginate();

        return TopicResource::collection($topics);
    }

    /**
     * 话题详情
     */
    public function show($topicId, TopicQuery $query)
    {
        // return new TopicResource($topic); 没法利用 include 参数

        $topic = $query->findOrFail($topicId);

        return new TopicResource($topic);
    }
}
