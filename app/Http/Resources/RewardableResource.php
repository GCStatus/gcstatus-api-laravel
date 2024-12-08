<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\{
    Title,
    Mission,
};

class RewardableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Rewardable $rewardable */
        $rewardable = $this->resource;

        return [
            'id' => $rewardable->id,
            'sourceable' => $this->getResourceForType($rewardable->sourceable),
            'rewardable' => $this->getResourceForType($rewardable->rewardable),
        ];
    }

    /**
     * Dynamically resolve the resource for the related model.
     *
     * @param \Illuminate\Database\Eloquent\Model|null $model
     * @return \Illuminate\Http\Resources\Json\JsonResource|array<mixed>
     */
    private function getResourceForType(?Model $model): JsonResource|array
    {
        if (!$model) {
            return new JsonResource([]);
        }

        switch (get_class($model)) {
            case Mission::class:
                return MissionResource::make($model);
            case Title::class:
                return TitleResource::make($model);
            default:
                return $model->toArray();
        }
    }
}
