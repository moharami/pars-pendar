<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Interfaces\ArticleRepositoryInterface;
use Illuminate\Http\Response;

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
        $article = $this->articleRepository->store($validatedData);
        return ApiResponseClass::sendResponse(new ArticleResource($article), 'Article created successfully', Response::HTTP_CREATED);
    }

}
