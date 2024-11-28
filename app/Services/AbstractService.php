<?php

namespace App\Services;

use App\Contracts\Services\AbstractServiceInterface;
use Illuminate\Database\Eloquent\{Model, Collection};
use App\Contracts\Repositories\AbstractRepositoryInterface;

abstract class AbstractService implements AbstractServiceInterface
{
    /**
     * Get the repository instance.
     *
     * @return \App\Contracts\Repositories\AbstractRepositoryInterface
     */
    abstract public function repository(): AbstractRepositoryInterface;

    /**
     * Get all repository records.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Model>
     */
    public function all(): Collection
    {
        return $this->repository()->all();
    }

    /**
     * Create a repository record.
     *
     * @param array<string, mixed> $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $data): Model
    {
        return $this->repository()->create($data);
    }

    /**
     * Find a repository record.
     *
     * @param mixed $id
     * @return ?\Illuminate\Database\Eloquent\Model
     */
    public function find(mixed $id): ?Model
    {
        return $this->repository()->find($id);
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
        return $this->repository()->findBy($field, $value);
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
        return $this->repository()->findAllBy($field, $value);
    }

    /**
     * Find a model collection where in array.
     *
     * @param array<int, mixed> $ids
     * @return \Illuminate\Database\Eloquent\Collection<int, Model>
     */
    public function findIn(array $ids): Collection
    {
        return $this->repository()->findIn($ids);
    }

    /**
     * Find or fail a repository record.
     *
     * @param mixed $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findOrFail(mixed $id): Model
    {
        return $this->repository()->findOrFail($id);
    }

    /**
     * Update a repository record.
     *
     * @param array<string, mixed> $data
     * @param mixed $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update(array $data, mixed $id): Model
    {
        return $this->repository()->update($data, $id);
    }

    /**
     * Delete the repository record.
     *
     * @param mixed $id
     * @return void
     */
    public function delete(mixed $id): void
    {
        $this->repository()->delete($id);
    }
}
