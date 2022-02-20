<?php

use App\Http\Controllers\Api\ArticleAuthorController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ArticleCategoryController;

Route::apiResource('articles', ArticleController::class);
Route::apiResource('categories', CategoryController::class)
    ->only('index', 'show');

Route::apiResource('authors', AuthorController::class)
    ->only('index', 'show');

Route::get('articles/{article}/relationships/category', [
    ArticleCategoryController::class, 'index'
])->name('articles.relationships.category');

Route::patch('articles/{article}/relationships/category', [
    ArticleCategoryController::class, 'update'
]);

Route::get('articles/{article}/category',  [
    ArticleCategoryController::class, 'show'
])->name('articles.category');

Route::get('articles/{article}/relationships/author', [
    ArticleAuthorController::class, 'index'
])->name('articles.relationships.author');

Route::patch('articles/{article}/relationships/author', [
    ArticleAuthorController::class, 'update'
]);

Route::get('articles/{article}/author', [
    ArticleAuthorController::class, 'show'
])->name('articles.author');
