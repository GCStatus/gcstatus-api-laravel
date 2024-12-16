<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Mission $mission */
        $mission = $this->resource;

        return [
            'id' => $mission->id,
            'coins' => $mission->coins,
            'mission' => $mission->mission,
            'for_all' => $mission->for_all,
            'frequency' => $mission->frequency,
            'experience' => $mission->experience,
            'description' => $mission->description,
            'status' => StatusResource::make($this->whenLoaded('status')),
            'progress' => UserMissionResource::make($this->whenLoaded('userMission')),
            'rewards' => RewardableResource::collection($this->whenLoaded('rewards')),
            'requirements' => MissionRequirementResource::collection($this->whenLoaded('requirements')),
        ];
    }
}
