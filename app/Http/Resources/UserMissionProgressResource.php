<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserMissionProgressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\UserMissionProgress $userMissionProgress */
        $userMissionProgress = $this->resource;

        return [
            'id' => $userMissionProgress->id,
            'progress' => $userMissionProgress->progress,
            'completed' => $userMissionProgress->completed,
            'user' => UserResource::make($this->whenLoaded('user')),
            'requirement' => MissionRequirementResource::make($this->whenLoaded('missionRequirement')),
        ];
    }
}
