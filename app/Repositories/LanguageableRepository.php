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
}
