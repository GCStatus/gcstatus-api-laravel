<?php

namespace App\Services;

use App\Contracts\Services\CriticServiceInterface;
use App\Contracts\Repositories\CriticRepositoryInterface;

class CriticService extends AbstractService implements CriticServiceInterface
{
    /**
     * @inheritDoc
     */
    public function repository(): CriticRepositoryInterface
    {
        return app(CriticRepositoryInterface::class);
    }
}
