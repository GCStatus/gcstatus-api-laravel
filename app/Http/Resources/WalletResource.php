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
        $wallet = $this;

        /** @var array<string, mixed> $arrayable */
        $arrayable = [
            'id' => $wallet->id,
            'amount' => $wallet->amount,
            'user' => UserResource::make($this->whenLoaded('user')),
        ];

        return $arrayable;
    }
}
