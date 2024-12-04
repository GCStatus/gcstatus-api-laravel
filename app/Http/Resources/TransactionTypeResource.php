<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\TransactionType $transactionType */
        $transactionType = $this->resource;

        return [
            'id' => $transactionType->id,
            'type' => $transactionType->type,
            'transactions' => TransactionResource::collection($this->whenLoaded('transactions')),
        ];
    }
}
