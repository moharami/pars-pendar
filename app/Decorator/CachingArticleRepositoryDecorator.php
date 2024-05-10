<?php

namespace App\Decorator;

use App\Interfaces\ArticleRepositoryInterface;
use App\Repositories\ArticleRepository;
use Illuminate\Support\Facades\Cache;

class CachingArticleRepositoryDecorator implements ArticleRepositoryInterface
{
    protected ArticleRepository $articleRepository;
    protected int $minutes = 60;

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function index($data)
    {
        $cacheKey = 'ar_'. md5(json_encode($data->all()));
        return Cache::remember($cacheKey, $this->minutes, function () use ($data) {
            return $this->articleRepository->index($data);
        });
    }

    public function getById($id)
    {
        $cacheKey = 'ar_by_id_' . $id;
        return Cache::remember($cacheKey, $this->minutes, function () use ($id) {
            return $this->articleRepository->getById($id);
        });
    }

    public function store(array $data)
    {
        return $this->articleRepository->store($data);
    }

    public function update(array $data, $id)
    {
        $this->forgetCacheByArticleId($id);
        return $this->articleRepository->update($data, $id);
    }

    public function delete($id)
    {
        $this->forgetCacheByArticleId($id);
        return $this->articleRepository->delete($id);
    }

    /**
     * Forget the cache related to a specific article ID
     *
     * @param $id
     */
    protected function forgetCacheByArticleId($id)
    {
        $cacheKey = 'ar_by_id_' . $id;
        Cache::forget($cacheKey);
    }
}
