<?php

namespace App\Http\Controllers\Api;

use App\Classes\ApiResponseClass;
use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleIndexRequest;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Resources\ArticleCollection;
use App\Http\Resources\ArticleResource;
use App\Interfaces\ArticleRepositoryInterface;
use App\Interfaces\FileRepositoryInterface;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    private ArticleRepositoryInterface $articleRepository;
    private FileRepositoryInterface $fileRepository;

    public function __construct(ArticleRepositoryInterface $articleRepository, FileRepositoryInterface $fileRepository)
    {
        $this->articleRepository = $articleRepository;
        $this->fileRepository = $fileRepository;
    }

    /**
     * @param ArticleIndexRequest $request
     * @return JsonResponse
     */
    public function index(ArticleIndexRequest $request): JsonResponse
    {

        $data = $this->articleRepository->index($request);
        return ApiResponseClass::sendResponse(new ArticleCollection($data));
    }


    /**
     * @param StoreArticleRequest $request
     * @return JsonResponse
     */
    public function store(StoreArticleRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $validatedData['user_id'] = auth()->id();
        if ($request->hasFile('image')) {
            $validatedData['image_path'] =$this->fileRepository->store($request->file('image'), 'article_images');
        }

        $article = $this->articleRepository->store($validatedData);
        return ApiResponseClass::sendResponse(new ArticleResource($article), 'Article created successfully', Response::HTTP_CREATED);
    }

    /**
     * @param Article $article
     * @return JsonResponse
     */
    public function show(Article $article): JsonResponse
    {
        return ApiResponseClass::sendResponse(new ArticleResource($article));
    }

    /**
     * @param Article $article
     * @return JsonResponse
     */
    public function destroy(Article $article): JsonResponse
    {
        if (Auth::user()->id !== $article->user_id) {
            return ApiResponseClass::sendResponse('Unauthorized', 'You are not authorized to delete this article.', Response::HTTP_FORBIDDEN);
        }

        $article->delete();

        return ApiResponseClass::sendResponse(null, 'Article deleted successfully');
    }

    /**
     * @param UpdateArticleRequest $request
     * @param Article $article
     * @return JsonResponse
     */
    public function update(UpdateArticleRequest $request, Article $article): JsonResponse
    {
        if (Auth::user()->id !== $article->user_id) {
            return ApiResponseClass::sendResponse('Unauthorized', 'You are not authorized to update this article.', Response::HTTP_FORBIDDEN);
        }

        $validatedData = $request->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $this->fileRepository->store($image, 'article_images');
            $validatedData['image_path'] = $imagePath;

            // Delete the old image if it exists
            if ($article->image_path) {
                $this->fileRepository->delete($article->image_path);
            }
        }

        $article->update($validatedData);

        return ApiResponseClass::sendResponse(new ArticleResource($article), 'Article updated successfully');
    }

}
