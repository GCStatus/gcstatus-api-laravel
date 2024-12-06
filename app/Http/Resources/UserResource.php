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
        $user = $this->resource;

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'nickname' => $user->nickname,
            'level' => $user->level?->level,
            'birthdate' => $user->birthdate,
            'experience' => $user->experience,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
            'wallet' => WalletResource::make($this->whenLoaded('wallet')),
            'profile' => ProfileResource::make($this->whenLoaded('profile')),
        ];
    }
}
