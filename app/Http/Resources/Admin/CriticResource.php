<?php

namespace App\Http\Resources\Admin;

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
            'logo' => $critic->logo,
            'slug' => $critic->slug,
            'acting' => $critic->acting,
            'created_at' => $critic->created_at,
            'updated_at' => $critic->updated_at,
        ];
    }
}
