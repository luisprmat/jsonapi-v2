<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryRelationshipTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function can_fetch_the_associated_category_identifier()
    {
        $article = Article::factory()->create();

        $url = route('api.v1.articles.relationships.category', $article);

        $response = $this->getJson($url);

        $response->assertExactJson([
            'data' => [
                'id' => $article->category->getRouteKey(),
                'type' => 'categories'
            ]
        ]);
    }

    /** @test */
    function can_fetch_the_associated_category_resource()
    {
        $article = Article::factory()->create();

        $url = route('api.v1.articles.category', $article);

        $response = $this->getJson($url);

        $response->assertJson([
            'data' => [
                'id' => $article->category->getRouteKey(),
                'type' => 'categories',
                'attributes' => [
                    'name' => $article->category->name
                ]
            ]
        ]);
    }

    /** @test  */
    function can_update_the_associated_category()
    {
        $category = Category::factory()->create();

        $article = Article::factory()->create();

        $url = route('api.v1.articles.relationships.category', $article);

        $this->withoutJsonApiDocumentFormatting();

        $response = $this->patchJson($url, [
            'data' => [
                'type' => 'categories',
                'id' => $category->getRouteKey()
            ]
        ]);

        $response->assertExactJson([
            'data' => [
                'type' => 'categories',
                'id' => $category->getRouteKey()
            ]
        ]);

        $this->assertDatabaseHas('articles', [
            'title' => $article->title,
            'category_id' => $category->id
        ]);
    }
}
