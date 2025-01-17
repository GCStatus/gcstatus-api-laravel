<?php

namespace App\Contracts\Services;

use Illuminate\Database\Eloquent\Collection;

interface BannerServiceInterface
{
    /**
     * Get all banners based on component.
     *
     * @param string $component
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Banner>
     */
    public function allBasedOnComponent(string $component): Collection;
}
