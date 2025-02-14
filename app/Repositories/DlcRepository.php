<?php

namespace App\Repositories;

use App\Models\Dlc;
use App\Contracts\Repositories\DlcRepositoryInterface;

class DlcRepository extends AbstractRepository implements DlcRepositoryInterface
{
    /**
     * The dlc model.
     *
     * @return \App\Models\Dlc
     */
    public function model(): Dlc
    {
        return new Dlc();
    }
}
