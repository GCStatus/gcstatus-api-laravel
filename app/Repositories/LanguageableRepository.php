<?php

namespace App\Repositories;

use App\Models\Languageable;
use App\Contracts\Repositories\LanguageableRepositoryInterface;

class LanguageableRepository extends AbstractRepository implements LanguageableRepositoryInterface
{
    /**
     * The languageable model.
     *
     * @return \App\Models\Languageable
     */
    public function model(): Languageable
    {
        return new Languageable();
    }

    /**
     * @inheritDoc
     */
    public function existsForPayload(array $data): bool
    {
        return $this->model()
            ->query()
            ->where('language_id', $data['language_id'])
            ->where('languageable_id', $data['languageable_id'])
            ->where('languageable_type', $data['languageable_type'])
            ->exists();
    }
}
