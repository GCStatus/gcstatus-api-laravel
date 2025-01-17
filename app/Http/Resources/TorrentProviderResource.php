<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TorrentProviderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\TorrentProvider $torrentProvider */
        $torrentProvider = $this->resource;

        return [
            'id' => $torrentProvider->id,
            'url' => $torrentProvider->url,
            'name' => $torrentProvider->name,
            'slug' => $torrentProvider->slug,
        ];
    }
}
