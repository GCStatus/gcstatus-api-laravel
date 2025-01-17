<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TorrentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Torrent $torrent */
        $torrent = $this->resource;

        return [
            'id' => $torrent->id,
            'url' => $torrent->url,
            'posted_at' => $torrent->posted_at,
            'game' => GameResource::make($this->whenLoaded('game')),
            'provider' => TorrentProviderResource::make($this->whenLoaded('provider')),
        ];
    }
}
