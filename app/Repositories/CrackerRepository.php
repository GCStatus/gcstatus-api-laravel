<?php

namespace App\Repositories;

use App\Models\Cracker;
use App\Contracts\Repositories\CrackerRepositoryInterface;

class CrackerRepository extends AbstractRepository implements CrackerRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function model(): Cracker
    {
        return new Cracker();
    }
}
