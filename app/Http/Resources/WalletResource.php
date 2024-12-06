<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Wallet $wallet */
        $wallet = $this->resource;

        return [
            'id' => $wallet->id,
            'balance' => $wallet->balance,
            'user' => UserResource::make($this->whenLoaded('user')),
        ];
    }
}
