<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Interfaces\ArticleRepositoryInterface;
use App\Models\Article;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    private ArticleRepositoryInterface $articleRepository;

    public function __construct(ArticleRepositoryInterface $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function index()
    {
        $data = $this->articleRepository->index();
        return ApiResponseClass::sendResponse(ArticleResource::collection($data));
    }


    public function store(StoreArticleRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['user_id'] = auth()->id();
        $article = $this->articleRepository->store($validatedData);
        return ApiResponseClass::sendResponse(new ArticleResource($article), 'Article created successfully', Response::HTTP_CREATED);
    }

    public function show(Article $article)
    {
        return ApiResponseClass::sendResponse(new ArticleResource($article));
    }

    public function delete(Article $article)
    {
        if (Auth::user()->id !== $article->user_id) {
            return ApiResponseClass::sendResponse('Unauthorized', 'You are not authorized to delete this article.', Response::HTTP_FORBIDDEN);
        }

        $article->delete();

        return ApiResponseClass::sendResponse(null, 'Article deleted successfully');
    }

}
