<?php

namespace App\Services;

use App\Contracts\Services\GenreServiceInterface;
use App\Contracts\Repositories\GenreRepositoryInterface;

class GenreService extends AbstractService implements GenreServiceInterface
{
    /**
     * The genre repository.
     *
     * @return \App\Contracts\Repositories\GenreRepositoryInterface
     */
    public function repository(): GenreRepositoryInterface
    {
        return app(GenreRepositoryInterface::class);
    }
}
