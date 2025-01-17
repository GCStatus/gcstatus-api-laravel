<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CriticResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Critic $critic */
        $critic = $this->resource;

        return [
            'id' => $critic->id,
            'url' => $critic->url,
            'name' => $critic->name,
            'slug' => $critic->slug,
            'acting' => $critic->acting,
        ];
    }
}
