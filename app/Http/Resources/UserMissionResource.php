<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserMissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\UserMission $userMission */
        $userMission = $this->resource;

        return [
            'id' => $userMission->id,
            'completed' => $userMission->completed,
            'last_completed_at' => $userMission->last_completed_at,
            'user' => UserResource::make($this->whenLoaded('user')),
            'mission' => MissionResource::make($this->whenLoaded('mission')),
        ];
    }
}
