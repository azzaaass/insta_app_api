<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'caption' => $this->caption,

            'image' => asset('storage/' . $this->image),

            'created_at' => $this->created_at,

            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'username' => $this->user->username,
            ],

            'likes_count' => $this->likes_count,

            'comments_count' => $this->comments_count,

            'is_liked' => auth()->check()
                ? $this->likes()->where('user_id', auth()->id())->exists()
                : false,
        ];
    }
}
