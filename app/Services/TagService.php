<?php

namespace App\Services;

use App\Contracts\Services\TagServiceInterface;
use App\Contracts\Repositories\TagRepositoryInterface;

class TagService extends AbstractService implements TagServiceInterface
{
    /**
     * @inheritDoc
     */
    public function repository(): TagRepositoryInterface
    {
        return app(TagRepositoryInterface::class);
    }
}
