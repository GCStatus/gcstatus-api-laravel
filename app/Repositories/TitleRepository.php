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
        return Title::query()
            ->with('rewardable.sourceable.requirements.userProgress')
            ->get();
    }

    /**
     * @inheritDoc
     */
    public function findOrFail(mixed $id): Title
    {
        /** @var \App\Models\Title $title */
        $title = Title::findOrFail($id);

        return $title;
    }
}
