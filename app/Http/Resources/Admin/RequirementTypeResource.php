<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequirementTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\RequirementType $requirementType */
        $requirementType = $this->resource;

        return [
            'id' => $requirementType->id,
            'os' => $requirementType->os,
            'potential' => $requirementType->potential,
            'created_at' => $requirementType->created_at,
            'updated_at' => $requirementType->updated_at,
        ];
    }
}
