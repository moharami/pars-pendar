<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Http\Requests\ArticleIndexRequest;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Resources\ArticleCollection;
use App\Http\Resources\ArticleResource;
use App\Interfaces\ArticleRepositoryInterface;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    private ArticleRepositoryInterface $articleRepository;

    public function __construct(ArticleRepositoryInterface $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function index(ArticleIndexRequest $request): JsonResponse
    {

        $data = $this->articleRepository->index($request);
        return ApiResponseClass::sendResponse(new ArticleCollection($data));
    }


    public function store(StoreArticleRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $validatedData['user_id'] = auth()->id();
        $article = $this->articleRepository->store($validatedData);
        return ApiResponseClass::sendResponse(new ArticleResource($article), 'Article created successfully', Response::HTTP_CREATED);
    }

    public function show(Article $article): JsonResponse
    {
        return ApiResponseClass::sendResponse(new ArticleResource($article));
    }

    public function destroy(Article $article): JsonResponse
    {
        if (Auth::user()->id !== $article->user_id) {
            return ApiResponseClass::sendResponse('Unauthorized', 'You are not authorized to delete this article.', Response::HTTP_FORBIDDEN);
        }

        $article->delete();

        return ApiResponseClass::sendResponse(null, 'Article deleted successfully');
    }

    public function update(UpdateArticleRequest $request, Article $article): JsonResponse
    {
        if (Auth::user()->id !== $article->user_id) {
            return ApiResponseClass::sendResponse('Unauthorized', 'You are not authorized to delete this article.', Response::HTTP_FORBIDDEN);
        }

        $article->update($request->validated());

        return ApiResponseClass::sendResponse(new ArticleResource($article), 'Article updated successfully');
    }

}
