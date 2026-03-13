<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'      => $this->id,
            'content' => $this->content,
            'author'  => [
                'id'           => $this->author->id,
                'name'         => $this->author->name,
                'display_role' => $this->author->display_role,
                'avatar'       => $this->author->avatar,
            ],
            'question' => $this->whenLoaded('question', fn() => [
                'id'    => $this->question->id,
                'title' => $this->question->title,
            ]),
            'created_at' => $this->created_at->diffForHumans(),
        ];
    }
}
