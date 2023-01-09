<?php

namespace App\Http\Controllers\Api;

use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\SaveCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Article;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum', [
            'only' => ['store']
        ]);
    }

    public function index()
    {
        $comments = Comment::paginate();

        return CommentResource::collection($comments);
    }

    public function store(SaveCommentRequest $request)
    {
        $comment = new Comment;

        $comment->body = $request->input('data.attributes.body');

        $comment->user_id = $request->getRelationshipId('author');

        $articleSlug = $request->getRelationshipId('article');
        $comment->article_id = Article::whereSlug($articleSlug)->firstOrFail()->id;

        $comment->save();

        return CommentResource::make($comment);
    }

    public function show(Comment $comment)
    {
        return CommentResource::make($comment);
    }

    public function update(Request $request, Comment $comment)
    {
        //
    }

    public function destroy(Comment $comment)
    {
        //
    }
}
