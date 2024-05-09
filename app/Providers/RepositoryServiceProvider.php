<?php

namespace App\Providers;

use App\Interfaces\ArticleRepositoryInterface;
use App\Repositories\ArticleRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(ArticleRepositoryInterface::class,ArticleRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
