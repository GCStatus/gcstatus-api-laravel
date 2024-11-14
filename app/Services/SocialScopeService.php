<?php

namespace App\Services;

use App\Models\SocialScope;
use App\Contracts\Services\SocialScopeServiceInterface;
use App\Contracts\Repositories\SocialScopeRepositoryInterface;

class SocialScopeService implements SocialScopeServiceInterface
{
    /**
     * The social account repository.
     *
     * @var \App\Contracts\Repositories\SocialScopeRepositoryInterface
     */
    private $socialScopeRepository;

    /**
     * Create a new class instance.
     *
     * @param \App\Contracts\Repositories\SocialScopeRepositoryInterface $socialScopeRepository
     * @return void
     */
    public function __construct(SocialScopeRepositoryInterface $socialScopeRepository)
    {
        $this->socialScopeRepository = $socialScopeRepository;
    }

    /**
     * Get the first social account or create if not exists.
     *
     * @param array<string, mixed> $searchable
     * @param array<string, mixed> $creatable
     * @return \App\Models\SocialScope
     */
    public function firstOrCreate(array $searchable, array $creatable): SocialScope
    {
        return $this->socialScopeRepository->firstOrCreate($searchable, $creatable);
    }
}
