<?php

namespace App\Repositories;

use App\Models\Publisher;
use App\Contracts\Repositories\PublisherRepositoryInterface;

class PublisherRepository extends AbstractRepository implements PublisherRepositoryInterface
{
    /**
     * The publisher model.
     *
     * @return \App\Models\Publisher
     */
    public function model(): Publisher
    {
        return new Publisher();
    }

    /**
     * @inheritDoc
     */
    public function existsByName(string $name): bool
    {
        return $this->model()
            ->query()
            ->where('name', $name)
            ->exists();
    }
}
