<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\CategoryController;
use Illuminate\Support\Facades\Route;

Route::apiResource('articles', ArticleController::class);
Route::apiResource('categories', CategoryController::class)
    ->only('index', 'show');

Route::apiResource('authors', AuthorController::class)
    ->only('index', 'show');

Route::get('articles/{article}/relationships/category', fn() => 'TODO')
    ->name('articles.relationships.category');

Route::get('articles/{article}/category', fn() => 'TODO')
    ->name('articles.category');
