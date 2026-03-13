<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'email'        => $this->email,
            'role'         => $this->role,
            'display_role' => $this->display_role,
            'avatar'       => $this->avatar,

            // Conditional fields
            $this->mergeWhen($this->isStudent(), [
                'class_name'   => $this->class_name,
                'class_number' => $this->class_number,
            ]),
            $this->mergeWhen($this->isTeacher(), [
                'subject' => $this->subject,
            ]),

            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
