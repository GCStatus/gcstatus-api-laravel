<?php

namespace App\Services;

use App\Contracts\Services\DeveloperServiceInterface;
use App\Contracts\Repositories\DeveloperRepositoryInterface;

class DeveloperService extends AbstractService implements DeveloperServiceInterface
{
    /**
     * The developer repository.
     *
     * @return \App\Contracts\Repositories\DeveloperRepositoryInterface
     */
    public function repository(): DeveloperRepositoryInterface
    {
        return app(DeveloperRepositoryInterface::class);
    }

    /**
     * @inheritDoc
     */
    public function existsByName(string $name): bool
    {
        return $this->repository()->existsByName($name);
    }
}
