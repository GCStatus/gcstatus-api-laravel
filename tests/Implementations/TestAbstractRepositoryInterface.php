<?php

namespace Tests\Implementations;

use Illuminate\Database\Eloquent\{Model, Collection};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Contracts\Repositories\AbstractRepositoryInterface;

class TestAbstractRepositoryInterface implements AbstractRepositoryInterface
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    /**
     * The related model.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected Model $model;

    /**
     * Create a new class instance.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * The implementation of abstract all method.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Model>
     */
    public function all(): Collection
    {
        return $this->model->all();
    }

    /**
     * The implementation of abstract create method.
     *
     * @param array<string, mixed> $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * The implementation of the find one nullable model method.
     *
     * @return ?\Illuminate\Database\Eloquent\Model
     */
    public function find(mixed $id): ?Model
    {
        /** @var ?\Illuminate\Database\Eloquent\Model $model */
        $model = $this->model->find($id);

        return $model;
    }

    /**
     * The implementation of the find many by array of ids method.
     *
     * @param array<int, mixed> $ids
     * @return \Illuminate\Database\Eloquent\Collection<int, Model>
     */
    public function findIn(array $ids): Collection
    {
        return $this->model->whereIn('id', $ids)->get();
    }

    /**
     * The implementation of the find one non-nullable model method.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findOrFail(mixed $id): Model
    {
        /** @var \Illuminate\Database\Eloquent\Model $model */
        $model = $this->model->findOrFail($id);

        return $model;
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
        $model = $this->findOrFail($id);

        $model->update($data);

        return $model;
    }

    /**
     * The implementation of abstract delete method.
     *
     * @param mixed $id
     * @return void
     */
    public function delete(mixed $id): void
    {
        $this->findOrFail($id)->delete();
    }
}
