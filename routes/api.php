<?php

use App\Http\Controllers\Api\ArticleCommentController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Article routes
Route::resource('articles', ArticleController::class);

// Comment rotes
Route::resource('articles.comments', ArticleCommentController::class);


Route::post('/comments/{comment}/like', [ArticleCommentController::class, 'toggleLike']);