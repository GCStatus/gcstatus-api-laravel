<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Transaction $transaction */
        $transaction = $this->resource;

        return [
            'id' => $transaction->id,
            'amount' => $transaction->amount,
            'description' => $transaction->description,
            'created_at' => $transaction->created_at,
            'user' => UserResource::make($this->whenLoaded('user')),
            'type' => TransactionTypeResource::make($this->whenLoaded('type')),
        ];
    }
}
