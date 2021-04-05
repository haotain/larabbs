<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
    public function index(Request $request, Topic $topic)
    {

        $topics = QueryBuilder::for(Topic::class)
            ->allowedIncludes('user', 'category')
            ->allowedFilters([
                'title',
                AllowedFilter::exact('category_id'),
                AllowedFilter::scope('withOrder')->default('recentReplied')
            ])
            ->paginate();

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
    public function userIndex(Request $request, User $user)
    {
        $query = $user->topics()->getQuery();

        $topics = QueryBuilder::for($query)
            ->allowedIncludes('user', 'category')
            ->allowedFilters([
                'title',
                AllowedFilter::exact('category_id'),
                AllowedFilter::scope('withOrder')->default('recentReplied')
            ])
            ->paginate();

        return TopicResource::collection($topics);
    }
}
