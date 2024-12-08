<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Contracts\Services\TitleOwnershipServiceInterface;

class TitleResource extends JsonResource
{
    /**
     * The title ownership service.
     *
     * @var \App\Contracts\Services\TitleOwnershipServiceInterface
     */
    private static ?TitleOwnershipServiceInterface $titleOwnershipService = null;

    /**
     * Cached ownership data.
     *
     * @var \Illuminate\Support\Collection<int, int>|null
     */
    private static ?\Illuminate\Support\Collection $ownershipCache = null;

    /**
     * Set the title ownership service.
     *
     * @param \App\Contracts\Services\TitleOwnershipServiceInterface $service
     * @return void
     */
    public static function setTitleOwnershipService(TitleOwnershipServiceInterface $service): void
    {
        self::$titleOwnershipService = $service;
    }

    /**
     * Preload ownership data for the given titles.
     *
     * @param array<mixed> $titles
     * @return void
     */
    public static function preloadOwnership(array $titles): void
    {
        /** @var array<int, int> $titleIds */
        $titleIds = collect($titles)->pluck('id')->toArray();

        if (self::$titleOwnershipService) {
            /** @var \App\Contracts\Services\TitleOwnershipServiceInterface $titleOwnershipService */
            $titleOwnershipService = self::$titleOwnershipService;

            self::$ownershipCache = $titleOwnershipService->areOwnedByCurrentUser($titleIds);
        }
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Title $title */
        $title = $this->resource;

        /** @var \App\Contracts\Services\TitleOwnershipServiceInterface $titleOwnershipService */
        $titleOwnershipService = self::$titleOwnershipService;

        $own = self::$ownershipCache
            ? self::$ownershipCache->contains($title->id)
            : $titleOwnershipService->isOwnedByCurrentUser($title);

        return [
            'id' => $title->id,
            'cost' => $title->cost,
            'own' => $own,
            'purchasable' => $title->purchasable,
            'description' => $title->description,
            'created_at' => $title->created_at,
            'updated_at' => $title->updated_at,
            'status' => StatusResource::make($this->whenLoaded('status')),
            'users' => UserResource::collection($this->whenLoaded('users')),
            'rewardable' => RewardableResource::make($this->whenLoaded('rewardable')),
        ];
    }
}
