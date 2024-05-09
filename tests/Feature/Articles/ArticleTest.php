<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
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
        $count = 3;

        Article::factory()->count($count)->create();

        $response = $this->get('/api/articles', $this->headers);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJsonStructure([
            'success',
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'content',
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
        $articleData = [
            'title' => 'Test Article',
            'content' => 'This is a test article content.'
        ];

        $response = $this->postJson('/api/articles', $articleData, $this->headers);

        $response->assertStatus(Response::HTTP_CREATED);

        $response->assertJsonStructure([
            'success',
            'data' => [
                'id',
                'title',
                'content',
            ],
        ]);

        $response->assertJson([
            'data' => $articleData
        ]);
    }

}