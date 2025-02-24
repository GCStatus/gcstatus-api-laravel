<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Storeable $storeable */
        $storeable = $this->resource;

        return [
            'id' => $storeable->id,
            'url' => $storeable->url,
            'price' => $storeable->price,
            'store_item_id' => $storeable->store_item_id,
            'created_at' => $storeable->created_at,
            'updated_at' => $storeable->updated_at,
            'store' => StoreResource::make($this->whenLoaded('store')),
        ];
    }
}
