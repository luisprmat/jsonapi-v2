<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;

class ArticleCommentsController extends Controller
{
    public function index(Article $article)
    {
        return CommentResource::identifiers($article->comments);
    }

    public function show(Article $article)
    {
        return CommentResource::collection($article->comments);
    }
}
