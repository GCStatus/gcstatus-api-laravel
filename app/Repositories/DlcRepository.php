<?php

namespace App\Repositories;

use App\Models\Dlc;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Contracts\Repositories\DlcRepositoryInterface;

class DlcRepository extends AbstractRepository implements DlcRepositoryInterface
{
    /**
     * The dlc model.
     *
     * @return \App\Models\Dlc
     */
    public function model(): Dlc
    {
        return new Dlc();
    }

    /**
     * @inheritDoc
     */
    public function allForAdmin(): Collection
    {
        return $this->model()
            ->query()
            ->with([ // @phpstan-ignore-line
                'game' => function (BelongsTo $query) {
                    $query->withoutEagerLoads();
                },
            ])->get();
    }

    /**
     * @inheritDoc
     */
    public function detailsForAdmin(mixed $id): Dlc
    {
        /** @var \App\Models\Dlc $dlc */
        $dlc = $this->model()
            ->query()
            ->with([ // @phpstan-ignore-line
                'game' => function (BelongsTo $query) {
                    $query->withoutEagerLoads();
                },
                'tags',
                'genres',
                'platforms',
                'categories',
                'publishers',
                'developers',
                'stores.store',
                'galleries.mediaType',
            ])->findOrFail($id);

        return $dlc;
    }
}
