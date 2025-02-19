<?php

namespace App\Repositories;

use App\Models\Tag;
use App\Contracts\Repositories\TagRepositoryInterface;

class TagRepository extends AbstractRepository implements TagRepositoryInterface
{
    /**
     * The tag model.
     *
     * @return \App\Models\Tag
     */
    public function model(): Tag
    {
        return new Tag();
    }
}
