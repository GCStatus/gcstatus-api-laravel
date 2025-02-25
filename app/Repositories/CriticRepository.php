<?php

namespace App\Repositories;

use App\Models\Critic;
use App\Contracts\Repositories\CriticRepositoryInterface;

class CriticRepository extends AbstractRepository implements CriticRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function model(): Critic
    {
        return new Critic();
    }
}
