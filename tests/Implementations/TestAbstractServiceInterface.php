<?php

namespace Tests\Implementations;

use App\Contracts\Services\AbstractServiceInterface;
use Illuminate\Database\Eloquent\{Model, Collection};
use App\Contracts\Repositories\AbstractRepositoryInterface;

class TestAbstractServiceInterface implements AbstractServiceInterface
{
    /**
     * The related model.
     *
     * @var \App\Contracts\Repositories\AbstractRepositoryInterface
     */
    protected AbstractRepositoryInterface $repository;

    /**
     * Create a new class instance.
     *
     * @param \App\Contracts\Repositories\AbstractRepositoryInterface $repository
     * @return void
     */
    public function __construct(AbstractRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * The implementation of abstract all method.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Model>
     */
    public function all(): Collection
    {
        return $this->repository->all();
    }

    /**
     * The implementation of abstract create method.
     *
     * @param array<string, mixed> $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $data): Model
    {
        return $this->repository->create($data);
    }

    /**
     * The implementation of the find one nullable model method.
     *
     * @return ?\Illuminate\Database\Eloquent\Model
     */
    public function find(mixed $id): ?Model
    {
        return $this->repository->find($id);
    }

    /**
     * The implementation of the find many by array of ids method.
     *
     * @param array<int, mixed> $ids
     * @return \Illuminate\Database\Eloquent\Collection<int, Model>
     */
    public function findIn(array $ids): Collection
    {
        return $this->repository->findIn($ids);
    }

    /**
     * The implementation of the find one non-nullable model method.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findOrFail(mixed $id): Model
    {
        return $this->repository->findOrFail($id);
    }

    /**
     * The implementation of abstract update method.
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
     * The implementation of abstract delete method.
     *
     * @param mixed $id
     * @return void
     */
    public function delete(mixed $id): void
    {
        $this->repository->delete($id);
    }
}
