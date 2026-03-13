<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'title'           => $this->title,
            'content'         => $this->content,
            'likes_count'     => $this->likes_count ?? $this->likes()->count(),
            'comments_count'  => $this->comments_count ?? 0,
            'bookmarks_count' => $this->bookmarks_count ?? $this->bookmarks()->count(),

            // Kalau user login, tampilin status like & bookmark dia
            'is_liked'      => (bool) ($this->is_liked ?? false),
            'is_bookmarked' => (bool) ($this->is_bookmarked ?? false),

            'author' => [
                'id'           => $this->author->id,
                'name'         => $this->author->name,
                'display_role' => $this->author->display_role,
                'avatar'       => $this->author->avatar,
            ],
            'comments'   => CommentResource::collection($this->whenLoaded('comments')),
            'created_at' => $this->created_at->diffForHumans(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
