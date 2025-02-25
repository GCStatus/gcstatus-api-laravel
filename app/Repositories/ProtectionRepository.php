<?php

namespace App\Repositories;

use App\Models\Protection;
use App\Contracts\Repositories\ProtectionRepositoryInterface;

class ProtectionRepository extends AbstractRepository implements ProtectionRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function model(): Protection
    {
        return new Protection();
    }
}
