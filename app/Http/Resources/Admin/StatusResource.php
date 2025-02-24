<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Status $status */
        $status = $this->resource;

        return [
            'id' => $status->id,
            'name' => $status->name,
            'created_at' => $status->created_at,
            'updated_at' => $status->updated_at,
        ];
    }
}
