<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublisherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Publisher $publisher */
        $publisher = $this->resource;

        return [
            'id' => $publisher->id,
            'name' => $publisher->name,
            'slug' => $publisher->slug,
            'acting' => $publisher->acting,
            'created_at' => $publisher->created_at,
            'updated_at' => $publisher->updated_at,
        ];
    }
}
