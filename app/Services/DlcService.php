<?php

namespace App\Services;

use App\Contracts\Services\DlcServiceInterface;
use App\Contracts\Repositories\DlcRepositoryInterface;

class DlcService extends AbstractService implements DlcServiceInterface
{
    /**
     * The dlc repository.
     *
     * @return \App\Contracts\Repositories\DlcRepositoryInterface
     */
    public function repository(): DlcRepositoryInterface
    {
        return app(DlcRepositoryInterface::class);
    }
}
