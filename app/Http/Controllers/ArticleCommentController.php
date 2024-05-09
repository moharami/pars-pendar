<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ArticleCommentController extends Controller
{
    public function index(Article $article)
    {
        $comments = $article->comments;
        return ApiResponseClass::sendResponse(CommentResource::collection($comments));
    }

    public function store(StoreCommentRequest $request, Article $article)
    {
        $validatedData = $request->validated();

        $comment = new Comment();
        $comment->content = $validatedData['content'];
        $comment->user_id = auth()->id();
        $article->comments()->save($comment);

        return ApiResponseClass::sendResponse(new CommentResource($comment), 'Comment created successfully', Response::HTTP_CREATED);
    }



    public function show(Article $article, Comment $comment)
    {
        return ApiResponseClass::sendResponse(new CommentResource($comment));
    }

    public function update(UpdateCommentRequest $request, Article $article, Comment $comment)
    {
        $comment->update($request->validated());
        return ApiResponseClass::sendResponse(new CommentResource($comment), 'Comment updated successfully');
    }

    public function destroy(Article $article, Comment $comment)
    {
        $comment->delete();
        return ApiResponseClass::sendResponse(null, 'Comment deleted successfully');
    }
}