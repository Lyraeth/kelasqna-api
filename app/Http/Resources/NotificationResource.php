<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'type'       => $this->data['type'] ?? null,
            'message'    => $this->data['message'] ?? null,
            'data'       => [
                'question_id'  => $this->data['question_id'] ?? null,
                'comment_id'   => $this->data['comment_id'] ?? null,
                'actor_name'   => $this->data['actor_name'] ?? null,
                'actor_avatar' => $this->data['actor_avatar'] ?? null,
            ],
            'is_read'    => !is_null($this->read_at),
            'created_at' => $this->created_at->diffForHumans(),
        ];
    }
}
