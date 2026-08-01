<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('user')
            ->withCount('likes')
            ->withCount('comments')
            ->latest()
            ->paginate(10);

        return PostResource::collection($posts);
    }

    public function store(StorePostRequest $request)
    {
        $image = $request
            ->file('image')
            ->store('posts', 'public');

        $post = Post::create([
            'user_id' => auth()->id(),
            'caption' => $request->caption,
            'image' => $image,
        ]);

        return new PostResource(
            $post->load('user')
        );
    }

    public function show(Post $post)
    {
        $post->load('user');

        $post->loadCount([
            'likes',
            'comments'
        ]);

        return new PostResource($post);
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        if ($post->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk mengubah post ini.'
            ], 403);
        }

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($post->image);

            $post->image = $request->file('image')->store('posts', 'public');
        }

        $post->caption = $request->caption;
        $post->save();

        return new PostResource($post->load('user'));
    }

    public function destroy(Post $post)
    {
        if ($post->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk menghapus post ini.'
            ], 403);
        }

        Storage::disk('public')->delete($post->image);

        $post->delete();

        return response()->json([
            'message' => 'Post berhasil dihapus.'
        ]);
    }

 public function userPosts(User $user)
{
    $posts = Post::where('user_id', $user->id)
        ->latest()
        ->with('user:id,name,username')
        ->withCount(['likes', 'comments'])
        ->get()
        ->map(function ($post) {

            $post->image = asset('storage/' . $post->image);

            return $post;

        });

    return response()->json([
        'data' => $posts
    ]);
}
}
