<?php

namespace App\Contracts\Repositories;

use App\Models\Dlc;
use Illuminate\Database\Eloquent\Collection;

interface DlcRepositoryInterface extends AbstractRepositoryInterface
{
    /**
     * Get all DLCs from platform for admin.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Dlc>
     */
    public function allForAdmin(): Collection;

    /**
     * Get a DLC details for admin.
     *
     * @param mixed $id
     * @return \App\Models\Dlc
     */
    public function detailsForAdmin(mixed $id): Dlc;
}
