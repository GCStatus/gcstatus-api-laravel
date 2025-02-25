<?php

namespace App\Services;

use App\Contracts\Services\MediaTypeServiceInterface;
use App\Contracts\Repositories\MediaTypeRepositoryInterface;

class MediaTypeService extends AbstractService implements MediaTypeServiceInterface
{
    /**
     * @inheritDoc
     */
    public function repository(): MediaTypeRepositoryInterface
    {
        return app(MediaTypeRepositoryInterface::class);
    }
}
