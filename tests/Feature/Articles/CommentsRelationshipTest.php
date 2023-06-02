<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommentsRelationshipTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_fetch_the_associated_comments_identifiers()
    {
        $article = Article::factory()->hasComments(2)->create();

        $url = route('api.v1.articles.relationships.comments', $article);

        $response = $this->getJson($url);

        $response->assertJsonCount(2, 'data');

        $article->comments->map(fn ($comment) => $response->assertJsonFragment([
            'id' => (string) $comment->getRouteKey(),
            'type' => 'comments',
        ]));
    }

    /** @test */
    public function it_returns_an_empty_array_when_there_are_no_associared_comments()
    {
        $article = Article::factory()->create();

        $url = route('api.v1.articles.relationships.comments', $article);

        $response = $this->getJson($url);

        $response->assertJsonCount(0, 'data');

        $response->assertExactJson([
            'data' => [],
        ]);
    }

    /** @test */
    public function can_fetch_the_associated_comments_resource()
    {
        $article = Article::factory()->hasComments(2)->create();

        $url = route('api.v1.articles.comments', $article);

        $response = $this->getJson($url);

        $response->assertJson([
            'data' => [
                [
                    'id' => $article->comments[0]->getRouteKey(),
                    'type' => 'comments',
                    'attributes' => [
                        'body' => $article->comments[0]->body,
                    ],
                ],
                [
                    'id' => $article->comments[1]->getRouteKey(),
                    'type' => 'comments',
                    'attributes' => [
                        'body' => $article->comments[1]->body,
                    ],
                ],
            ],
        ]);
    }
}
