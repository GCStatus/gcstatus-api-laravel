<?php

namespace App\Services;

use App\Contracts\Services\PlatformServiceInterface;
use App\Contracts\Repositories\PlatformRepositoryInterface;

class PlatformService extends AbstractService implements PlatformServiceInterface
{
    /**
     * @inheritDoc
     */
    public function repository(): PlatformRepositoryInterface
    {
        return app(PlatformRepositoryInterface::class);
    }
}
