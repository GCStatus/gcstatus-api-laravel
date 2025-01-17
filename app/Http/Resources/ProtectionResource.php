<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProtectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Protection $protection */
        $protection = $this->resource;

        return [
            'id' => $protection->id,
            'name' => $protection->name,
            'slug' => $protection->slug,
        ];
    }
}
