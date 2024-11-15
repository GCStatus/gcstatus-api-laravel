<?php

namespace App\Services;

use App\Models\SocialAccount;
use App\Contracts\Services\SocialAccountServiceInterface;
use App\Contracts\Repositories\SocialAccountRepositoryInterface;

class SocialAccountService implements SocialAccountServiceInterface
{
    /**
     * The social account repository.
     *
     * @var \App\Contracts\Repositories\SocialAccountRepositoryInterface
     */
    private $socialAccountRepository;

    /**
     * Create a new class instance.
     *
     * @param \App\Contracts\Repositories\SocialAccountRepositoryInterface $socialAccountRepository
     * @return void
     */
    public function __construct(SocialAccountRepositoryInterface $socialAccountRepository)
    {
        $this->socialAccountRepository = $socialAccountRepository;
    }

    /**
     * Get the first social account or create if not exists.
     *
     * @param array<string, mixed> $searchable
     * @param array<string, mixed> $creatable
     * @return \App\Models\SocialAccount
     */
    public function firstOrCreate(array $searchable, array $creatable): SocialAccount
    {
        return $this->socialAccountRepository->firstOrCreate($searchable, $creatable);
    }
}
