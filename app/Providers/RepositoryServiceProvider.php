<?php

namespace App\Providers;

use App\Decorator\CachingArticleRepositoryDecorator;
use App\Interfaces\ArticleRepositoryInterface;
use App\Interfaces\FileRepositoryInterface;
use App\Repositories\ArticleRepository;
use App\Repositories\LocalFileRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $cacheEnabled = Config('article.cache_article');
        $this->app->bind(
            ArticleRepositoryInterface::class,
            $cacheEnabled ? CachingArticleRepositoryDecorator::class : ArticleRepository::class
        );

        $this->app->bind(FileRepositoryInterface::class, LocalFileRepository::class);

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
