<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ArticleCommentController;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::get('articles', [ArticleController::class, 'index']);
Route::post('articles', [ArticleController::class, 'store']);
Route::get('articles/{article}', [ArticleController::class, 'show']);
Route::delete('articles/{article}', [ArticleController::class, 'delete']);
Route::put('articles/{article}', [ArticleController::class, 'update']);


// Comment routes
Route::get('articles/{article}/comments', [ArticleCommentController::class, 'index']);
Route::post('articles/{article}/comments', [ArticleCommentController::class, 'store']);
Route::get('articles/{article}/comments/{comment}', [ArticleCommentController::class, 'show']);
Route::put('articles/{article}/comments/{comment}', [ArticleCommentController::class, 'update']);
Route::delete('articles/{article}/comments/{comment}', [ArticleCommentController::class, 'destroy']);
