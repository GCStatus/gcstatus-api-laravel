<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequirementableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Requirementable $requirementable */
        $requirementable = $this->resource;

        return [
            'id' => $requirementable->id,
            'os' => $requirementable->os,
            'dx' => $requirementable->dx,
            'cpu' => $requirementable->cpu,
            'gpu' => $requirementable->gpu,
            'ram' => $requirementable->ram,
            'rom' => $requirementable->rom,
            'obs' => $requirementable->obs,
            'network' => $requirementable->network,
            'type' => RequirementTypeResource::make($this->whenLoaded('requirementType')),
        ];
    }
}
