<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Store $store */
        $store = $this->resource;

        return [
            'id' => $store->id,
            'url' => $store->url,
            'name' => $store->name,
            'slug' => $store->slug,
            'logo' => $store->logo,
        ];
    }
}
