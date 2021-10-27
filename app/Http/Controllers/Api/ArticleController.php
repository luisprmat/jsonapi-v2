<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveArticleRequest;
use App\Http\Resources\ArticleCollection;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Response;

class ArticleController extends Controller
{
    public function index(): ArticleCollection
    {
        $articles = Article::query();

        // filters
        $allowedFilters = ['title', 'content', 'year', 'month'];

        foreach (request('filter', []) as $filter => $value) {
            abort_unless(in_array($filter, $allowedFilters), 400);
            if ($filter === 'year') {
                $articles->whereYear('created_at', $value);
            } else if ($filter === 'month') {
                $articles->whereMonth('created_at', $value);
            } else {
                $articles->where($filter, 'LIKE', '%' . $value . '%');
            }
        }

        $articles->allowedSorts(['title', 'content']);

        return ArticleCollection::make($articles->jsonPaginate());
    }

    public function store(SaveArticleRequest $request): ArticleResource
    {
        $article = Article::create($request->validated());

        return ArticleResource::make($article);
    }

    public function show(Article $article): ArticleResource
    {
        return ArticleResource::make($article);
    }

    public function update(Article $article, SaveArticleRequest $request): ArticleResource
    {
        $article->update($request->validated());

        return ArticleResource::make($article);
    }

    public function destroy(Article $article): Response
    {
        $article->delete();

        return response()->noContent();
    }
}
