<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function toggle(Post $post)
    {
        $like = Like::where('user_id', auth()->id())
            ->where('post_id', $post->id)
            ->first();

        if ($like) {

            $like->delete();

            return response()->json([
                'liked' => false,
                'message' => 'Unlike berhasil'
            ]);
        }

        Like::create([
            'user_id' => auth()->id(),
            'post_id' => $post->id,
        ]);

        return response()->json([
            'liked' => true,
            'message' => 'Like berhasil'
        ]);
    }
}
