<?php

namespace Tests\Feature\Articles;

use App\Http\Resources\CommentResource;
use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class ArticleCommentTest extends TestCase
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
     * Test retrieving comments for an article.
     *
     * @return void
     */
    public function test_index_comments()
    {
        $user = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $user->id]);

        $comments = Comment::factory()->count(3)->create([
            'commentable_id' => $article->id,
            'commentable_type' => Article::class,
            'user_id' => $user->id,
        ]);

        $response = $this->get("/api/articles/{$article->id}/comments", $this->headers);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'success',
            'data' => [
                '*' => [
                    'id',
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

        $this->assertCount(3, $responseData['data']);
        $this->assertEquals($comments->first()->content, $responseData['data'][0]['content']);
        $this->assertEquals($user->id, $responseData['data'][0]['user']['id']);
    }

    public function test_store_comment()
    {
        $user = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $commentData = [
            'content' => 'This is a new comment',
        ];

        $response = $this->postJson("/api/articles/{$article->id}/comments", $commentData, $this->headers);

        $responseData = $response->json();

        $response->assertStatus(Response::HTTP_CREATED);

        // Asserting the success flag in the response
        $this->assertTrue($responseData['success']);

        // Asserting the message in the response
        $this->assertEquals('Comment created successfully', $responseData['message']);

        // Asserting the structure of the response data
        $response->assertJsonStructure([
            'success',
            'data' => [
                'id',
                'content',
                'user' => [
                    'id',
                    'name',
                    'email',
                ],
            ],
        ]);

        // Asserting the persistence of the comment in the database
        $this->assertDatabaseHas('comments', [
            'content' => $commentData['content'],
            'commentable_id' => $article->id,
            'commentable_type' => Article::class,
            'user_id' => $user->id,
        ]);
    }


    /**
     * Test retrieving a single comment for an article.
     *
     * @return void
     */
    public function test_show_comment()
    {
        $user = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $user->id]);
        $comment = Comment::factory()->create([
            'commentable_id' => $article->id,
            'commentable_type' => Article::class,
            'user_id' => $user->id,
        ]);

        $response = $this->get("/api/articles/{$article->id}/comments/{$comment->id}", $this->headers);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'id',
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
        $this->assertEquals($comment->id, $responseData['data']['id']);
        $this->assertEquals($comment->content, $responseData['data']['content']);
        $this->assertEquals($user->id, $responseData['data']['user']['id']);
    }

    /**
     * Test updating a comment for an article.
     *
     * @return void
     */
    public function test_update_comment()
    {
        $user = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $user->id]);
        $comment = Comment::factory()->create([
            'commentable_id' => $article->id,
            'commentable_type' => Article::class,
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        $updatedData = [
            'content' => 'This is an updated comment',
        ];

        $response = $this->putJson("/api/articles/{$article->id}/comments/{$comment->id}", $updatedData, $this->headers);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'id',
                'content',
            ],
        ]);

        $responseData = $response->json();

        $this->assertTrue($responseData['success']);
        $this->assertEquals('Comment updated successfully', $responseData['message']);
        $this->assertEquals($updatedData['content'], $responseData['data']['content']);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'content' => $updatedData['content'],
            'commentable_id' => $article->id,
            'commentable_type' => Article::class,
            'user_id' => $user->id,
        ]);
    }

    /**
     * Test deleting a comment for an article.
     *
     * @return void
     */
    public function test_delete_comment()
    {
        $user = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $user->id]);
        $comment = Comment::factory()->create([
            'commentable_id' => $article->id,
            'commentable_type' => Article::class,
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        $response = $this->deleteJson("/api/articles/{$article->id}/comments/{$comment->id}", [], $this->headers);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure(['success', 'message']);

        $responseData = $response->json();

        $this->assertTrue($responseData['success']);
        $this->assertEquals('Comment deleted successfully', $responseData['message']);
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }
}