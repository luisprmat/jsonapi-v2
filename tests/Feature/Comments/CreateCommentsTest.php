<?php

namespace Tests\Feature\Comments;

use Tests\TestCase;
use App\Models\User;
use App\Models\Article;
use App\Models\Comment;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateCommentsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_cannot_create_comments()
    {
        $this->postJson(route('api.v1.comments.store'))
            ->assertJsonApiError(
                title: 'Unauthenticated',
                detail: 'This action requires authentication.',
                status: '401'
            );

        $this->assertDatabaseCount('comments', 0);
    }

    /** @test */
    public function can_create_comments()
    {
        $user = User::factory()->create();
        $article = Article::factory()->create();

        Sanctum::actingAs($user);

        $this->assertDatabaseCount('comments', 0);

        $response = $this->postJson(route('api.v1.comments.store'), [
            'body' => $commentBody = 'Comment body',
            '_relationships' => [
                'article' => $article,
                'author' => $user
            ]
        ])->assertCreated();

        $comment = Comment::first();

        $response->assertJsonApiResource($comment, [
            'body' => $commentBody
        ]);

        $this->assertDatabaseCount('comments', 1)
            ->assertDatabaseHas('comments', [
                'body' => $commentBody,
                'article_id' => $article->id,
                'user_id' => $user->id
            ]);
    }
}
