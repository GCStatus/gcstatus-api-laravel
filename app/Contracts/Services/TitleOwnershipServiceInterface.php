<?php

namespace App\Contracts\Services;

use App\Models\Title;
use Illuminate\Support\Collection;

interface TitleOwnershipServiceInterface
{
    /**
     * Get is owned by current user attribute.
     *
     * @param \App\Models\Title $title
     * @return bool
     */
    public function isOwnedByCurrentUser(Title $title): bool;


    /**
     * Check if multiple titles are owned by the current user.
     *
     * @param array<int, int> $titleIds
     * @return \Illuminate\Support\Collection<int, int>
     */
    public function areOwnedByCurrentUser(array $titleIds): ?Collection;
}
