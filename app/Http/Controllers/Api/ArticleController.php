<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Http\Requests\SaveArticleRequest;
use App\Models\Category;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ArticleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum', [
            'only' => ['store', 'update', 'destroy'],
        ]);
    }

    public function index(): AnonymousResourceCollection
    {
        $articles = Article::query()
            ->allowedIncludes(['category', 'author'])
            ->allowedFilters(['title', 'content', 'year', 'month', 'categories'])
            ->allowedSorts(['title', 'content'])
            ->sparseFieldset()
            ->jsonPaginate();

        return ArticleResource::collection($articles);
    }

    public function store(SaveArticleRequest $request): ArticleResource
    {
        $this->authorize('create', new Article);

        $data = $request->validated()['data'];

        $articleData = $data['attributes'];

        $articleData['user_id'] = $data['relationships']['author']['data']['id'];

        $categorySlug = $data['relationships']['category']['data']['id'];

        $category = Category::whereSlug($categorySlug)->first();

        $articleData['category_id'] = $category->id;

        $article = Article::create($articleData);

        return ArticleResource::make($article);
    }

    public function show($article): JsonResource
    {
        $article = Article::where('slug', $article)
            ->allowedIncludes(['category', 'author'])
            ->sparseFieldset()
            ->firstOrFail();

        return ArticleResource::make($article);
    }

    public function update(Article $article, SaveArticleRequest $request): ArticleResource
    {
        $this->authorize('update', $article);

        $data = $request->validated()['data'];

        $articleData = $data['attributes'];

        if (isset($articleData['relationships'])) {
            if (isset($articleData['relationships']['author'])) {
                $articleData['user_id'] = $data['relationships']['author']['data']['id'];
            }

            if (isset($articleData['relationships']['category'])) {
                $categorySlug = $data['relationships']['category']['data']['id'];

                $category = Category::whereSlug($categorySlug)->first();

                $articleData['category_id'] = $category->id;
            }
        }

        $article->update($articleData);

        return ArticleResource::make($article);
    }

    public function destroy(Article $article, Request $request): Response
    {
        $this->authorize('delete', $article);

        $article->delete();

        return response()->noContent();
    }
}
