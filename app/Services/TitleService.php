<?php

namespace App\Services;

use App\Models\Title;
use Illuminate\Database\Eloquent\Collection;
use App\Contracts\Services\TitleServiceInterface;
use App\Contracts\Repositories\TitleRepositoryInterface;

class TitleService implements TitleServiceInterface
{
    /**
     * The title repository.
     *
     * @var \App\Contracts\Repositories\TitleRepositoryInterface
     */
    private TitleRepositoryInterface $titleRepository;

    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->titleRepository = app(TitleRepositoryInterface::class);
    }

    /**
     * @inheritDoc
     */
    public function allForUser(): Collection
    {
        return $this->titleRepository->allForUser();
    }

    /**
     * @inheritDoc
     */
    public function findOrFail(mixed $id): Title
    {
        return $this->titleRepository->findOrFail($id);
    }
}
