<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeveloperResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Developer $developer */
        $developer = $this->resource;

        return [
            'id' => $developer->id,
            'name' => $developer->name,
            'slug' => $developer->slug,
            'acting' => $developer->acting,
        ];
    }
}
