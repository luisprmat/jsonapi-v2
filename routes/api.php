<?php

use App\Http\Controllers\Api\ArticleController;
use Illuminate\Support\Facades\Route;

Route::apiResource('articles', ArticleController::class)
    ->names('api.v1.articles');
