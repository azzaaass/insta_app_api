<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Post $post)
    {
        $comments = $post->comments()
            ->with('user')
            ->latest()
            ->get();

        return CommentResource::collection($comments);
    }

    public function store(StoreCommentRequest $request, Post $post)
    {
        $comment = Comment::create([
            'user_id' => auth()->id(),
            'post_id' => $post->id,
            'comment' => $request->comment,
        ]);

        return new CommentResource(
            $comment->load('user')
        );
    }

    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        if ($comment->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses.'
            ], 403);
        }

        $comment->update([
            'comment' => $request->comment,
        ]);

        return new CommentResource(
            $comment->load('user')
        );
    }

    public function destroy(Comment $comment)
    {
        if ($comment->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses.'
            ], 403);
        }

        $comment->delete();

        return response()->json([
            'message' => 'Komentar berhasil dihapus.'
        ]);
    }
}
