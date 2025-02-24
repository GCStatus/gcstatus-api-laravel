<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SocialUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\User $user */
        $user = $this->resource;

        /** @var \App\Models\Profile $profile */
        $profile = $user->profile;

        return [
            'id' => $user->id,
            'name' => $user->name,
            'nickname' => $user->nickname,
            'level' => $user->level?->level,
            'photo' => storage()->getPath($profile->photo),
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];
    }
}
