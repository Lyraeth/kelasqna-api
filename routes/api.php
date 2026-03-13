<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Comment\CommentController;
use App\Http\Controllers\Api\Question\LikeBookmarkController;
use App\Http\Controllers\Api\Question\QuestionController;
use App\Http\Controllers\Api\Session\SessionController;
use App\Http\Controllers\Api\User\UserController;
use Illuminate\Support\Facades\Route;

// ─── Public Routes ───────────────────────────────────────────
Route::post('/login',    [AuthController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);

Route::get('/users/{user}',           [UserController::class, 'show']);
Route::get('/users/{user}/questions', [UserController::class, 'userQuestions']);

// ─── Protected Routes ─────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/questions',      [QuestionController::class, 'index']);
    Route::get('/questions/{id}', [QuestionController::class, 'show']);

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/me',      [UserController::class, 'me']);
    Route::get('/me/questions', [UserController::class, 'myQuestions']);
    Route::get('/me/comments',  [UserController::class, 'myComments']);
    Route::get('/me/bookmarks', [LikeBookmarkController::class, 'myBookmarks']);

    Route::get('/sessions',    [SessionController::class, 'devices']);
    Route::delete('/sessions', [SessionController::class, 'revokeSession']);

    // Question write: butuh token dengan ability 'question:write'
    Route::middleware('ability:question:write')->group(function () {
        Route::post('/questions',              [QuestionController::class, 'store']);
        Route::put('/questions/{question}',    [QuestionController::class, 'update']);
        Route::delete('/questions/{question}', [QuestionController::class, 'destroy']);
    });

    Route::middleware('ability:comment:write')->group(function () {
        Route::post('/questions/{question}/comments',              [CommentController::class, 'store']);
        Route::put('/questions/{question}/comments/{comment}',    [CommentController::class, 'update']);
        Route::delete('/questions/{question}/comments/{comment}', [CommentController::class, 'destroy']);
        Route::post('/questions/{question}/like',     [LikeBookmarkController::class, 'toggleLike']);
        Route::post('/questions/{question}/bookmark', [LikeBookmarkController::class, 'toggleBookmark']);
    });
});
