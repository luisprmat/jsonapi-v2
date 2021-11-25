<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\CategoryController;
use Illuminate\Support\Facades\Route;

Route::apiResource('articles', ArticleController::class);

Route::apiResource('categories', CategoryController::class)
    ->only('index', 'show');
