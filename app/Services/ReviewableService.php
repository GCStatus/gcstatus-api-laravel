<?php

namespace App\Services;

use App\Contracts\Services\ReviewableServiceInterface;
use App\Contracts\Repositories\ReviewableRepositoryInterface;

class ReviewableService extends AbstractService implements ReviewableServiceInterface
{
    /**
     * The reviewable service.
     *
     * @return \App\Contracts\Repositories\ReviewableRepositoryInterface
     */
    public function repository(): ReviewableRepositoryInterface
    {
        return app(ReviewableRepositoryInterface::class);
    }
}
