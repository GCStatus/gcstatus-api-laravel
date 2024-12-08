<?php

namespace App\Services;

use App\Models\Title;
use Illuminate\Support\Collection;
use App\Contracts\Services\{
    AuthServiceInterface,
    TitleOwnershipServiceInterface,
};
use Illuminate\Database\Eloquent\Builder;

class TitleOwnershipService implements TitleOwnershipServiceInterface
{
    /**
     * The auth service.
     *
     * @var \App\Contracts\Services\AuthServiceInterface
     */
    private AuthServiceInterface $authService;

    /**
     * Cached ownership data.
     *
     * @var Collection<(int|string), mixed>|null
     */
    private ?Collection $ownershipData = null;

    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authService = app(AuthServiceInterface::class);
    }

    /**
     * Load ownership data for the current user and titles.
     *
     * @param array<int, int> $titleIds
     * @return void
     */
    private function loadOwnershipData(array $titleIds): void
    {
        $userId = $this->authService->getAuthId();

        if ($this->ownershipData === null || !empty(array_diff($titleIds, $this->ownershipData->toArray()))) {
            $this->ownershipData = Title::whereIn('id', $titleIds)
                ->whereHas('users', fn (Builder $query) => $query->where('users.id', $userId))
                ->pluck('id');
        }
    }

    /**
     * Get is owned by current user attribute.
     *
     * @param \App\Models\Title $title
     * @return bool
     */
    public function isOwnedByCurrentUser(Title $title): bool
    {
        $this->loadOwnershipData([$title->id]);

        return $this->ownershipData && $this->ownershipData->contains($title->id);
    }

    /**
     * Check if multiple titles are owned by the current user.
     *
     * @param array<int, int> $titleIds
     * @return Collection<(int|string), mixed>|null
     */
    public function areOwnedByCurrentUser(array $titleIds): ?Collection
    {
        $this->loadOwnershipData($titleIds);

        return $this->ownershipData;
    }
}
