<?php

namespace App\Repositories;

use App\Models\Platform;
use App\Contracts\Repositories\PlatformRepositoryInterface;

class PlatformRepository extends AbstractRepository implements PlatformRepositoryInterface
{
    /**
     * The platform model.
     *
     * @return \App\Models\Platform
     */
    public function model(): Platform
    {
        return new Platform();
    }
}
