<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\{Model, Collection};
use App\Contracts\Repositories\AbstractRepositoryInterface;

abstract class AbstractRepository implements AbstractRepositoryInterface
{
    /**
     * Get the model instance.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    abstract public function model(): Model;

    /**
     * Get all model records.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Model>
     */
    public function all(): Collection
    {
        return $this->model()->orderByDesc('created_at')->get();
    }

    /**
     * Create a model record.
     *
     * @param array<string, mixed> $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $data): Model
    {
        return $this->model()->create($data);
    }

    /**
     * Find a model record.
     *
     * @param mixed $id
     * @return ?\Illuminate\Database\Eloquent\Model
     */
    public function find(mixed $id): ?Model
    {
        return $this->model()->where('id', $id)->first();
    }

    /**
     * Find a model collection where in array.
     *
     * @param array<int, mixed> $ids
     * @return \Illuminate\Database\Eloquent\Collection<int, Model>
     */
    public function findIn(array $ids): Collection
    {
        return $this->model()->whereIn('id', $ids)->get();
    }

    /**
     * Find or fail a model record.
     *
     * @param mixed $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findOrFail(mixed $id): Model
    {
        /** @var \Illuminate\Database\Eloquent\Model $model */
        $model = $this->model()->findOrFail($id);

        return $model;
    }

    /**
     * Update a model record.
     *
     * @param array<string, mixed> $data
     * @param mixed $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update(array $data, mixed $id): Model
    {
        $model = $this->findOrFail($id);

        $model->update($data);

        return $model;
    }

    /**
     * Delete the model record.
     *
     * @param mixed $id
     * @return void
     */
    public function delete(mixed $id): void
    {
        $this->model()->delete();
    }
}
