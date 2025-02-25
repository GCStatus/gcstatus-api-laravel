<?php

namespace App\Repositories;

use App\Models\MediaType;
use App\Contracts\Repositories\MediaTypeRepositoryInterface;

class MediaTypeRepository extends AbstractRepository implements MediaTypeRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function model(): MediaType
    {
        return new MediaType();
    }
}
