<?php

namespace App\Services;

use App\Contracts\Services\ProtectionServiceInterface;
use App\Contracts\Repositories\ProtectionRepositoryInterface;

class ProtectionService extends AbstractService implements ProtectionServiceInterface
{
    /**
     * @inheritDoc
     */
    public function repository(): ProtectionRepositoryInterface
    {
        return app(ProtectionRepositoryInterface::class);
    }
}
