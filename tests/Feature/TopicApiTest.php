<?php

namespace Tests\Feature;

use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\ActingJWTUser;

class TopicApiTest extends TestCase
{
    use RefreshDatabase;
    // use ActingJWTUser;

    protected $user;

    protected function setUp() : void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /**
     * 创建话题
     */
    public function testStoreTopic()
    {
        $data = ['category_id' => 1, 'body' => 'test body', 'title' => 'test title'];

        $token = auth('api')->fromUser($this->user);
        $response = $this->withHeaders(['Authorization' => 'Bearer '.$token])
            ->json('POST', '/api/v1/topics', $data);

        // $response = $this->JWTActingAs($this->user)->json('post', '/api/v1/topics', $data);


        $assertData = [
            'category_id' => 1,
            'user_id' => $this->user->id,
            'title' => 'test title',
            'body' => clean('test body', 'user_topic_body'),
        ];

        $response->assertStatus(201)
            ->assertJsonFragment($assertData);
    }

    /**
     * 更新話題
     */
    public function testUpdateTopic()
    {
        $topic = $this->makeTopic();

        $editData = ['category_id' => 2, 'body' => 'edit body', 'title' => 'edit title'];

        $token = auth('api')->fromUser($this->user);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
                        ->json('PATCH', '/api/v1/topics/' . $topic->id, $editData);

        $assertData = [
            'category_id' => 2,
            'user_id' => $this->user->id,
            'title' => 'edit title',
            'body' => clean('edit body', 'user_topic_body')
        ];

        $response->assertStatus(200)
            ->assertJsonFragment($assertData);

    }

    protected  function maketopic()
    {
        return Topic::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => 1
        ]);
    }

    /**
     * 查看话题
     */
    public function testShowTopic()
    {
        $topic = $this->maketopic();

        $response = $this->json('get', '/api/v1/topics/' . $topic->id);

        $assertData = [
            'category_id' => $topic->category_id,
            'user_id' => $topic->user_id,
            'title' => $topic->title,
            'body' => $topic->body,
        ];

        $response->assertStatus(200)->assertJsonFragment($assertData);
    }

    /**
     * 话题列表
     */
    public function testIndexTopic()
    {
        $response = $this->json('get', '/api/v1/topics');

        $response->assertStatus(200)->assertJsonStructure(['data', 'meta']);
    }

    /**
     * 删除话题
     */
    public function testDeleteTopic()
    {
        $topic = $this->maketopic();

        $token = auth('api')->fromUser($this->user);
        $response = $this->withHeaders(['Authorization' => 'Bearer' . $token])->json('delete', '/api/v1/topics/'. $topic->id);

        $response->assertStatus(204);

        $response = $this->json('GET', '/api/v1/topics/'.$topic->id);
        $response->assertStatus(404);
    }


}
