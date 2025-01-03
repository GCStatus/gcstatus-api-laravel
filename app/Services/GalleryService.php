<?php

namespace App\Services;

use App\Contracts\Services\GalleryServiceInterface;
use App\Contracts\Repositories\GalleryRepositoryInterface;

class GalleryService extends AbstractService implements GalleryServiceInterface
{
    /**
     * The gallery repository.
     *
     * @return \App\Contracts\Repositories\GalleryRepositoryInterface
     */
    public function repository(): GalleryRepositoryInterface
    {
        return app(GalleryRepositoryInterface::class);
    }
}
