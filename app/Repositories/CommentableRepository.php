<?php

namespace App\Repositories;

use App\Models\Commentable;
use App\Contracts\Repositories\CommentableRepositoryInterface;

class CommentableRepository extends AbstractRepository implements CommentableRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function model(): Commentable
    {
        return new Commentable();
    }
}
