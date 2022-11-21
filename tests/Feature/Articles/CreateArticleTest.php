<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\User;
use App\Models\Article;
use App\Models\Category;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateArticleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_cannot_create_articles()
    {
        $response = $this->postJson(route('api.v1.articles.store'))
            ->assertJsonApiError(
                title: 'Unauthenticated',
                detail: 'This action requires authentication.',
                status: '401'
            );

        $this->assertDatabaseCount('articles', 0);
    }

    /** @test */
    public function can_create_articles()
    {
        $user = User::factory()->create();

        $category = Category::factory()->create();

        Sanctum::actingAs($user, ['article:create']);

        $response = $this->postJson(route('api.v1.articles.store'), [
            'title' => 'New Article',
            'slug' => 'new-article',
            'content' => 'Content of the Article',
            '_relationships' => [
                'category' => $category,
                'author' => $user,
            ],
        ])->assertCreated();

        $article = Article::first();

        $response->assertJsonApiResource($article, [
            'title' => 'New Article',
            'slug' => 'new-article',
            'content' => 'Content of the Article',
        ]);

        $this->assertDatabaseHas('articles', [
            'title' => 'New Article',
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);
    }

    /** @test */
    public function title_is_required()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.articles.store'), [
            'slug' => 'new-article',
            'content' => 'Content of the Article',
        ])->assertJsonApiValidationErrors('title');
    }

    /** @test */
    public function title_must_be_at_least_4_characters()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'New',
            'slug' => 'new-article',
            'content' => 'Content of the Article',
        ])->assertJsonApiValidationErrors('title');
    }

    /** @test */
    public function slug_is_required()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'New Article',
            'content' => 'Content of the Article',
        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_be_unique()
    {
        Sanctum::actingAs(User::factory()->create());

        $article = Article::factory()->create();

        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'New Article',
            'slug' => $article->slug,
            'content' => 'Content of the Article',
        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_only_contain_letters_numbers_and_dashes()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'New Article',
            'slug' => '$%^&',
            'content' => 'Content of the Article',
        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_not_contain_letters_underscores()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'New Article',
            'slug' => 'with_underscores',
            'content' => 'Content of the Article',
        ])->assertSee(__('validation.no_underscores', [
            'attribute' => 'data.attributes.slug', ]
        ))->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_not_start_with_dashes()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'New Article',
            'slug' => '-start-with-dashes',
            'content' => 'Content of the Article',
        ])->assertSee(__('validation.no_starting_dashes', [
            'attribute' => 'data.attributes.slug', ]
        ))->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_not_end_with_dashes()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'New Article',
            'slug' => 'end-with-dashes-',
            'content' => 'Content of the Article',
        ])->assertSee(__('validation.no_ending_dashes', [
            'attribute' => 'data.attributes.slug', ]
        ))->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function content_is_required()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'New Article',
            'slug' => 'new-article',
        ])->assertJsonApiValidationErrors('content');
    }

    /** @test */
    public function category_relationship_is_required()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'New Article',
            'slug' => 'new-article',
            'content' => 'Article content',
        ])->assertJsonApiValidationErrors('relationships.category');
    }

    /** @test */
    public function category_must_exist_in_database()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'New Article',
            'slug' => 'new-article',
            'content' => 'Article content',
            '_relationships' => [
                'category' => Category::factory()->make(),
            ],
        ])->assertJsonApiValidationErrors('relationships.category');
    }
}
