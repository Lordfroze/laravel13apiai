<?php

use App\Http\Controllers\ArticleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:api')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');

    Route::get('/articles', [ArticleController::class, 'index']);
    Route::get('/articles/{article}', [ArticleController::class, 'show']);
    Route::post('/articles', [ArticleController::class, 'store'])->middleware('throttle:write');
    Route::put('/articles/{article}', [ArticleController::class, 'update'])->middleware('throttle:write');
    Route::delete('/articles/{article}', [ArticleController::class, 'destroy'])->middleware('throttle:write');

    Route::post('/articles/{article}/summarize', [ArticleController::class, 'summarize'])->middleware('throttle:strict');
});
