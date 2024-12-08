<?php

namespace App\Repositories;

use App\Models\Title;
use Illuminate\Database\Eloquent\Collection;
use App\Contracts\Repositories\TitleRepositoryInterface;

class TitleRepository implements TitleRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function allForUser(): Collection
    {
        return Title::all();
    }
}
