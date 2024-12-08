<?php

namespace App\Contracts\Services;

use Illuminate\Database\Eloquent\Collection;

interface TitleServiceInterface
{
    /**
     * Get all titles for authenticated user.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Title>
     */
    public function allForUser(): Collection;
}
