<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use App\Contracts\Services\BannerServiceInterface;
use App\Contracts\Repositories\BannerRepositoryInterface;

class BannerService implements BannerServiceInterface
{
    /**
     * The banner repository.
     *
     * @var \App\Contracts\Repositories\BannerRepositoryInterface
     */
    private BannerRepositoryInterface $bannerRepository;

    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->bannerRepository = app(BannerRepositoryInterface::class);
    }

    /**
     * @inheritDoc
     */
    public function allBasedOnComponent(string $component): Collection
    {
        return $this->bannerRepository->allBasedOnComponent($component);
    }
}
