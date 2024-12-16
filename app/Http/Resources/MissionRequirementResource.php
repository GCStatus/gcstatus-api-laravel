<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MissionRequirementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\MissionRequirement $requirement */
        $requirement = $this->resource;

        return [
            'id' => $requirement->id,
            'goal' => $requirement->goal,
            'task' => $requirement->task,
            'description' => $requirement->description,
            'created_at' => $requirement->created_at,
            'updated_at' => $requirement->updated_at,
            'mission' => MissionResource::make($this->whenLoaded('mission')),
            'progress' => UserMissionProgressResource::make($this->whenLoaded('userProgress')),
            'progresses' => UserMissionProgressResource::collection($this->whenLoaded('progresses')),
        ];
    }
}
