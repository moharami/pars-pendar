<?php

namespace Tests\Feature\Articles;

use App\Http\Resources\UserResource;
use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
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
        $count  = 3;
        $user = User::factory()->create();
        $this->actingAs($user);

        Article::factory()->count($count)->forUserId($user->id)->create();

        $response = $this->get('/api/articles', $this->headers);


        $response->assertStatus(Response::HTTP_OK);

        $response->assertJsonStructure([
            'success',
            'data' => [
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
        ]);

        $responseData = $response->json();

        $this->assertCount($count, $responseData['data']);
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

}