<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Http\Resources\ArticleResource;
use App\Interfaces\ArticleRepositoryInterface;

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

}
