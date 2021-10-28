<?php

use App\Http\Controllers\Api\ArticleController;
use Illuminate\Support\Facades\Route;

Route::bind('article', function ($article) {
    return App\Models\Article::where('slug', $article)
        ->sparseFieldset()
        ->firstOrFail();
});

Route::apiResource('articles', ArticleController::class)
    ->names('api.v1.articles');
