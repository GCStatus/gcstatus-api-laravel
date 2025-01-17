<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameSupportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\GameSupport $gameSupport */
        $gameSupport = $this->resource;

        return [
            'id' => $gameSupport->id,
            'url' => $gameSupport->url,
            'email' => $gameSupport->email,
            'contact' => $gameSupport->contact,
            'game' => GameResource::make($this->whenLoaded('game')),
        ];
    }
}
