<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CrackResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Crack $crack */
        $crack = $this->resource;

        return [
            'id' => $crack->id,
            'cracked_at' => $crack->cracked_at,
            'game' => GameResource::make($this->whenLoaded('game')),
            'status' => StatusResource::make($this->whenLoaded('status')),
            'cracker' => CrackerResource::make($this->whenLoaded('cracker')),
            'protection' => ProtectionResource::make($this->whenLoaded('protection')),
        ];
    }
}
