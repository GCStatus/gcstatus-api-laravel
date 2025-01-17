<?php

namespace App\Contracts\Repositories;

use Illuminate\Database\Eloquent\Collection;

interface BannerRepositoryInterface
{
    /**
     * Get all banners based on component.
     *
     * @param string $component
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Banner>
     */
    public function allBasedOnComponent(string $component): Collection;
}
