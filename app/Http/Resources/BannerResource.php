<?php

namespace App\Http\Resources;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Banner $banner */
        $banner = $this->resource;

        return [
            'id' => $banner->id,
            'type' => $banner->bannerable_type,
            'bannerable' => $this->getResourceForType($banner->bannerable),
        ];
    }

    /**
     * Dynamically resolve the resource for the related model.
     *
     * @param \Illuminate\Database\Eloquent\Model|null $model
     * @return \Illuminate\Http\Resources\Json\JsonResource|array<mixed>
     */
    public function getResourceForType(?Model $model): JsonResource|array
    {
        if (!$model) {
            return new JsonResource([]);
        }

        switch (get_class($model)) {
            case Game::class:
                return GameResource::make($model);
            default:
                return $model->toArray();
        }
    }
}
