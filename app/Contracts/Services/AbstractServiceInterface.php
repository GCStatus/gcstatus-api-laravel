<?php

namespace App\Contracts\Services;

use Illuminate\Database\Eloquent\{Model, Collection};

interface AbstractServiceInterface
{
    /**
     * Should have method all.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Model>
     */
    public function all(): Collection;

    /**
     * Should have the create method.
     *
     * @param array<string, mixed> $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $data): Model;

    /**
     * Should have method find.
     *
     * @param mixed $id
     * @return ?\Illuminate\Database\Eloquent\Model
     */
    public function find(mixed $id): ?Model;

    /**
     * Find a model collection where in array.
     *
     * @param array<int, mixed> $ids
     * @return \Illuminate\Database\Eloquent\Collection<int, Model>
     */
    public function findIn(array $ids): Collection;

    /**
     * Should have method find or fail.
     *
     * @param mixed $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findOrFail(mixed $id): Model;

    /**
     * Should have update method.
     *
     * @param array<string, mixed> $data
     * @param mixed $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update(array $data, mixed $id): Model;

    /**
     * Should have delete method.
     *
     * @param mixed $id
     * @return void
     */
    public function delete(mixed $id): void;
}
