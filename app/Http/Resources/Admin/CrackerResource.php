<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CrackerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Cracker $cracker */
        $cracker = $this->resource;

        return [
            'id' => $cracker->id,
            'name' => $cracker->name,
            'slug' => $cracker->slug,
            'acting' => $cracker->acting,
            'created_at' => $cracker->created_at,
            'updated_at' => $cracker->updated_at,
        ];
    }
}
