<?php

namespace App\Repositories;

use App\Models\Language;
use App\Contracts\Repositories\LanguageRepositoryInterface;

class LanguageRepository extends AbstractRepository implements LanguageRepositoryInterface
{
    /**
     * The language model.
     *
     * @return \App\Models\Language
     */
    public function model(): Language
    {
        return new Language();
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
