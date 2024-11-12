<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\User $user */
        $user = $this;

        /** @var array<string, mixed> $arrayable */
        $arrayable = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'nickname' => $user->nickname,
            'level' => $user->level?->level,
            'birthdate' => $user->birthdate,
            'experience' => $user->experience,
        ];

        return $arrayable;
    }
}
