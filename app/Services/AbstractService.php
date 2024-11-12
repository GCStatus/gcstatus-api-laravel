<?php

namespace App\Services;

use Illuminate\Database\Eloquent\{Model, Collection};
use App\Contracts\Repositories\AbstractRepositoryInterface;

abstract class AbstractService
{
    /**
     * The abstract repository interface.
     *
     * @var \App\Contracts\Repositories\AbstractRepositoryInterface
     */
    protected AbstractRepositoryInterface $repository;

    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->repository = $this->resolve();
    }

    /**
     * Get all repository records.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Model>
     */
    public function all(): Collection
    {
        return $this->repository->all();
    }

    /**
     * Create a repository record.
     *
     * @param array<string, mixed> $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $data): Model
    {
        return $this->repository->create($data);
    }

    /**
     * Find a repository record.
     *
     * @param mixed $id
     * @return ?\Illuminate\Database\Eloquent\Model
     */
    public function find(mixed $id): ?Model
    {
        return $this->repository->find($id);
    }

    /**
     * Find a model collection where in array.
     *
     * @param array<int, mixed> $ids
     * @return \Illuminate\Database\Eloquent\Collection<int, Model>
     */
    public function findIn(array $ids): Collection
    {
        return $this->repository->findIn($ids);
    }

    /**
     * Find or fail a repository record.
     *
     * @param mixed $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findOrFail(mixed $id): Model
    {
        return $this->repository->findOrFail($id);
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
        return $this->repository->update($data, $id);
    }

    /**
     * Delete the repository record.
     *
     * @param mixed $id
     * @return void
     */
    public function delete(mixed $id): void
    {
        $this->repository->delete($id);
    }

    /**
     * Resolve the repository instance.
     *
     * @return \App\Contracts\Repositories\AbstractRepositoryInterface
     */
    public function resolve(): AbstractRepositoryInterface
    {
        // $repository would be initialized as class-string type.
        /** @var string $repository */
        $repository = $this->repository;

        return resolve($repository);
    }
}
