<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ArticleCommentController extends Controller
{
    /**
     * @param Article $article
     * @return JsonResponse
     */
    public function index(Article $article): JsonResponse
    {
        $comments = $article->comments;
        return ApiResponseClass::sendResponse(CommentResource::collection($comments));
    }

    /**
     * @param StoreCommentRequest $request
     * @param Article $article
     * @return JsonResponse
     */
    public function store(StoreCommentRequest $request, Article $article): JsonResponse
    {
        $validatedData = $request->validated();

        $comment = new Comment();
        $comment->content = $validatedData['content'];
        $comment->user_id = auth()->id();
        $article->comments()->save($comment);

        return ApiResponseClass::sendResponse(new CommentResource($comment), 'Comment created successfully', Response::HTTP_CREATED);
    }


    /**
     * @param Article $article
     * @param Comment $comment
     * @return JsonResponse
     */
    public function show(Article $article, Comment $comment): JsonResponse
    {
        return ApiResponseClass::sendResponse(new CommentResource($comment));
    }

    /**
     * @param UpdateCommentRequest $request
     * @param Article $article
     * @param Comment $comment
     * @return JsonResponse
     */
    public function update(UpdateCommentRequest $request, Article $article, Comment $comment): JsonResponse
    {
        $comment->update($request->validated());
        return ApiResponseClass::sendResponse(new CommentResource($comment), 'Comment updated successfully');
    }

    /**
     * @param Article $article
     * @param Comment $comment
     * @return JsonResponse
     */
    public function destroy(Article $article, Comment $comment): JsonResponse
    {
        $comment->delete();
        return ApiResponseClass::sendResponse(null, 'Comment deleted successfully');
    }


    /**
     * @param Request $request
     * @param Comment $comment
     * @return JsonResponse
     */
    public function toggleLike(Request $request, Comment $comment): JsonResponse
    {
        $user = $request->user();

        if ($comment->likes()->where('user_id', $user->id)->exists()) {
            $comment->likes()->detach($user->id);
        } else {
            $comment->likes()->attach($user->id, ['action' => 'like']);
        }

        return response()->json(['message' => 'Like updated'], 200);
    }

}
