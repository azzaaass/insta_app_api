<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
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
        if ($request->hasFile('image')) {

            Storage::disk('public')->delete($post->image);

            $post->image = $request
                ->file('image')
                ->store('posts', 'public');
        }

        $post->caption = $request->caption;

        $post->save();

        return new PostResource(
            $post->load('user')
        );
    }

    public function destroy(Post $post)
    {
        Storage::disk('public')->delete($post->image);

        $post->delete();

        return response()->json([
            'message' => 'Post berhasil dihapus'
        ]);
    }
}
