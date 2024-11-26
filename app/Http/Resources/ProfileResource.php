<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Profile $profile */
        $profile = $this;

        /** @var array<string, mixed> $arrayable */
        $arrayable = [
            'id' => $profile->id,
            'photo' => storage()->getPath($profile->photo),
            'share' => $profile->share,
            'phone' => $profile->phone,
            'twitch' => $profile->twitch,
            'github' => $profile->github,
            'twitter' => $profile->twitter,
            'youtube' => $profile->youtube,
            'facebook' => $profile->facebook,
            'instagram' => $profile->instagram,
            'user' => UserResource::make($this->whenLoaded('user')),
        ];

        return $arrayable;
    }
}
