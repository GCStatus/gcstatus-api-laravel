<?php

namespace App\Contracts\Repositories;

use Illuminate\Database\Eloquent\{Model, Collection};

interface AbstractRepositoryInterface
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
     * Get first model record or create based on given attributes.
     *
     * @param array<string, mixed> $attributes
     * @param array<string, mixed> $values
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function firstOrCreate(array $attributes, array $values = []): Model;

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
     * Find a mode by a given field.
     *
     * @param string $field
     * @param mixed $value
     * @return ?\Illuminate\Database\Eloquent\Model
     */
    public function findBy(string $field, mixed $value): ?Model;

    /**
     * Find all models by given field.
     *
     * @param string $field
     * @param mixed $value
     * @return \Illuminate\Database\Eloquent\Collection<int, Model>
     */
    public function findAllBy(string $field, mixed $value): Collection;

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
