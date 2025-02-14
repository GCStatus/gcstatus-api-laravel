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
     * Get first model record or create based on given attributes.
     *
     * @param array<string, mixed> $attributes
     * @param array<string, mixed> $values
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function firstOrCreate(array $attributes, array $values = []): Model
    {
        return $this->model()->firstOrCreate($attributes, $values);
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
     * Find a mode by a given field.
     *
     * @param string $field
     * @param mixed $value
     * @return ?\Illuminate\Database\Eloquent\Model
     */
    public function findBy(string $field, mixed $value): ?Model
    {
        return $this->model()->where($field, $value)->first();
    }

    /**
     * Find all models by given field.
     *
     * @param string $field
     * @param mixed $value
     * @return \Illuminate\Database\Eloquent\Collection<int, Model>
     */
    public function findAllBy(string $field, mixed $value): Collection
    {
        return $this->model()->where($field, $value)->get();
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
        $model = $this->findOrFail($id);

        $model->delete();
    }
}
