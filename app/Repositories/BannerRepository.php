<?php

namespace App\Repositories;

use App\Models\Banner;
use Illuminate\Database\Eloquent\Collection;
use App\Contracts\Repositories\BannerRepositoryInterface;

class BannerRepository implements BannerRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function allBasedOnComponent(string $component): Collection
    {
        return Banner::query()
            ->where('component', $component)
            ->get();
    }
}
