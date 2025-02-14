<?php

namespace App\Services;

use App\Contracts\Services\PublisherServiceInterface;
use App\Contracts\Repositories\PublisherRepositoryInterface;

class PublisherService extends AbstractService implements PublisherServiceInterface
{
    /**
     * The publisher repository.
     *
     * @return \App\Contracts\Repositories\PublisherRepositoryInterface
     */
    public function repository(): PublisherRepositoryInterface
    {
        return app(PublisherRepositoryInterface::class);
    }

    /**
     * @inheritDoc
     */
    public function existsByName(string $name): bool
    {
        return $this->repository()->existsByName($name);
    }
}
