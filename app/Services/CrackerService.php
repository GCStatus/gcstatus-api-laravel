<?php

namespace App\Services;

use App\Contracts\Services\CrackerServiceInterface;
use App\Contracts\Repositories\CrackerRepositoryInterface;

class CrackerService extends AbstractService implements CrackerServiceInterface
{
    /**
     * @inheritDoc
     */
    public function repository(): CrackerRepositoryInterface
    {
        return app(CrackerRepositoryInterface::class);
    }
}
