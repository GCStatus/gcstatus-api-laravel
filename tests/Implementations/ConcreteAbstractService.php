<?php

namespace Tests\Implementations;

use App\Services\AbstractService;
use App\Contracts\Services\AbstractServiceInterface;
use App\Contracts\Repositories\AbstractRepositoryInterface;

class ConcreteAbstractService extends AbstractService implements AbstractServiceInterface
{
    /**
     * The repository instance.
     *
     * @var \App\Contracts\Repositories\AbstractRepositoryInterface
     */
    protected AbstractRepositoryInterface $repository;

    /**
     * Create a new instance of the service.
     *
     * @param \App\Contracts\Repositories\AbstractRepositoryInterface $repository
     */
    public function __construct(AbstractRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * The repository instance.
     *
     * @return \App\Contracts\Repositories\AbstractRepositoryInterface
     */
    public function repository(): AbstractRepositoryInterface
    {
        return $this->repository;
    }
}
