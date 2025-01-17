<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlatformResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Platform $platform */
        $platform = $this->resource;

        return [
            'id' => $platform->id,
            'slug' => $platform->slug,
            'name' => $platform->name,
        ];
    }
}
