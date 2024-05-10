<?php

namespace Tests\Feature\Articles;

use App\Http\Requests\ArticleIndexRequest;
use App\Http\Resources\UserResource;
use App\Jobs\SendEmailToAdminJob;
use App\Jobs\SendSMSToAdminJob;
use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ArticleTest extends TestCase
{


    use RefreshDatabase;

    private array $headers;


    public function setUp(): void
    {
        parent::setUp();
        $this->refreshDatabase();
        $this->headers = ['Accept' => 'application/json', 'Content-type' => 'application/json'];
        $this->headers = $this->transformHeadersToServerVars($this->headers);
    }


    /**
     * Test retrieving a list of articles.
     *
     * @return void
     */
    public function test_index_articles()
    {
        $count = 3;
        $user = User::factory()->create();
        $this->actingAs($user);

        Article::factory()->count($count)->forUserId($user->id)->create();

        $response = $this->get('/api/articles', $this->headers);
        $responseJson = $response->json();

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJsonStructure([
            'success',
            'data' => [

                'item' =>
                    [
                        '*' => [
                            'id',
                            'title',
                            'content',
                            'user' => [
                                'id',
                                'name',
                                'email',
                            ],
                        ],
                    ],

            ],
        ]);

        $responseData = $response->json();

        $this->assertCount($count, $responseJson['data']['item']);
    }


    /**
     * Test creating a new article.
     *
     * @return void
     */
    public function test_create_article()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $articleData = [
            'title' => 'Test Article',
            'content' => 'This is a test article content.',
        ];

        $response = $this->postJson('/api/articles', $articleData, $this->headers);
        $responseData = $response->json();

        $this->assertTrue($responseData['success']);
        $this->assertEquals('Article created successfully', $responseData['message']);

        $this->assertDatabaseHas('articles', [
            'title' => $articleData['title'],
            'content' => $articleData['content'],
            'user_id' => $user->id,
        ]);

    }


    public function test_show_article()
    {
        $user = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $user->id]);

        $response = $this->get("/api/articles/{$article->id}", $this->headers);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'id',
                'title',
                'content',
                'user' => [
                    'id',
                    'name',
                    'email',
                ],
            ],
        ]);

        $responseData = $response->json();

        $this->assertTrue($responseData['success']);
        $this->assertEquals($article->id, $responseData['data']['id']);
        $this->assertEquals($article->title, $responseData['data']['title']);
        $this->assertEquals($article->content, $responseData['data']['content']);
        $this->assertEquals($user->id, $responseData['data']['user']['id']);
        $this->assertEquals($user->name, $responseData['data']['user']['name']);
        $this->assertEquals($user->email, $responseData['data']['user']['email']);
    }


    public function test_delete_article()
    {
        $user = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->deleteJson("/api/articles/{$article->id}", [], $this->headers);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure(['success', 'message']);

        $responseData = $response->json();

        $this->assertTrue($responseData['success']);
        $this->assertEquals('Article deleted successfully', $responseData['message']);
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }


    public function test_unauthorized_user_cannot_delete_article()
    {
        $user = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $user->id]);

        $unauthorizedUser = User::factory()->create();
        $this->actingAs($unauthorizedUser);

        $response = $this->deleteJson("/api/articles/{$article->id}", [], $this->headers);


        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $responseData = $response->json();

        $this->assertEquals('You are not authorized to delete this article.', $responseData['message']);

        $this->assertDatabaseHas('articles', ['id' => $article->id]);
    }


    public function test_update_article()
    {
        $user = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $updatedData = [
            'title' => 'Updated Article Title',
            'content' => 'Updated Article Content',
        ];

        $response = $this->putJson("/api/articles/{$article->id}", $updatedData, $this->headers);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure(['success', 'data' => ['id', 'title', 'content']]);

        $responseData = $response->json();

        $this->assertTrue($responseData['success']);
        $this->assertEquals('Article updated successfully', $responseData['message']);
        $this->assertEquals($updatedData['title'], $responseData['data']['title']);
        $this->assertEquals($updatedData['content'], $responseData['data']['content']);

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'title' => $updatedData['title'],
            'content' => $updatedData['content'],
            'user_id' => $user->id,
        ]);
    }


    public function test_filters_articles_by_title_results()
    {
        Article::factory()->create(['title' => 'Test Article amir']);
        Article::factory()->create(['title' => 'Test Article ali']);
        Article::factory()->create(['title' => 'Test Article ali2']);

        $response = $this->get("/api/articles/" . '?title=ali&page=1');

        $response->assertStatus(Response::HTTP_OK);
        $this->assertCount(2, $response->json('data'));

        $response = $this->get("/api/articles/" . '?title=amir');
        $responseJson = $response->json();

        $this->assertCount(1, $responseJson['data']['item']);

        $response = $this->get("/api/articles/" . '?title=Test');
        $responseJson = $response->json();
        $this->assertCount(3, $responseJson['data']['item']);
    }


    public function test_filters_articles_by_title_results_with_cache_decorator()
    {
        Article::factory()->create(['title' => 'Test Article amir']);
        $data = ['title'=>'amir', 'page'=>"1"];
        $response = $this->get("/api/articles/?". http_build_query($data));
        $response->assertStatus(Response::HTTP_OK);
        $cacheKey = 'ar_'. md5(json_encode($data));
        $this->assertCount(1, $response->json('data.item'));
        if (Config('article.cache_article')){
            $this->assertTrue(Config('article.cache_article') && Cache::has($cacheKey));
        }else{
            $this->assertFalse(Config('article.cache_article') && Cache::has($cacheKey));
        }
    }


    public function test_create_article_job()
    {
        Queue::fake();

        $user = User::factory()->create();
        $this->actingAs($user);

        $articleData = [
            'title' => 'Test Article',
            'content' => 'This is a test article content.',
        ];

        $this->postJson('/api/articles', $articleData, $this->headers);

        Queue::assertPushedOn('default', SendEmailToAdminJob::class);
        Queue::assertPushedOn('default', SendSMSToAdminJob::class);
    }



}