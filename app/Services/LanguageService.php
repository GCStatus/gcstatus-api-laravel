<?php

namespace App\Services;

use App\Contracts\Services\LanguageServiceInterface;
use App\Contracts\Repositories\LanguageRepositoryInterface;

class LanguageService extends AbstractService implements LanguageServiceInterface
{
    /**
     * The language repository.
     *
     * @return \App\Contracts\Repositories\LanguageRepositoryInterface
     */
    public function repository(): LanguageRepositoryInterface
    {
        return app(LanguageRepositoryInterface::class);
    }

    /**
     * @inheritDoc
     */
    public function existsByName(string $name): bool
    {
        return $this->repository()->existsByName($name);
    }
}
