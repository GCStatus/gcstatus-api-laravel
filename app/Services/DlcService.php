<?php

namespace App\Services;

use App\Models\Dlc;
use Illuminate\Database\Eloquent\Collection;
use App\Contracts\Services\DlcServiceInterface;
use App\Contracts\Repositories\DlcRepositoryInterface;

class DlcService extends AbstractService implements DlcServiceInterface
{
    /**
     * The dlc repository.
     *
     * @return \App\Contracts\Repositories\DlcRepositoryInterface
     */
    public function repository(): DlcRepositoryInterface
    {
        return app(DlcRepositoryInterface::class);
    }

    /**
     * @inheritDoc
     */
    public function allForAdmin(): Collection
    {
        return $this->repository()->allForAdmin();
    }

    /**
     * @inheritDoc
     */
    public function detailsForAdmin(mixed $id): Dlc
    {
        return $this->repository()->detailsForAdmin($id);
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): Dlc
    {
        $data['about'] = clean($data['about']);
        $data['description'] = clean($data['description']);

        /** @var \App\Models\Dlc */
        return $this->repository()->create($data);
    }

    /**
     * @inheritDoc
     */
    public function update(array $data, mixed $id): Dlc
    {
        if (isset($data['about'])) {
            $data['about'] = clean($data['about']);
        }

        if (isset($data['description'])) {
            $data['description'] = clean($data['description']);
        }

        /** @var \App\Models\Dlc $dlc */
        $dlc = $this->repository()->update($data, $id);

        $this->syncRelationships($dlc, $data);

        return $dlc;
    }

    /**
     * Sync the relations of DLC.
     *
     * @param \App\Models\Dlc $dlc
     * @param array<string, mixed> $data
     * @return void
     */
    private function syncRelationships(Dlc $dlc, array $data): void
    {
        $relations = [
            'tags',
            'genres',
            'platforms',
            'categories',
            'publishers',
            'developers',
        ];

        foreach ($relations as $relation) {
            if (array_key_exists($relation, $data)) {
                $dlc->{$relation}()->sync($data[$relation] ?? []);
            }
        }
    }
}
