<?php

namespace App\Contracts\Methods;

interface ExistsByNameInterface
{
    /**
     * Check if a model exists by name.
     *
     * @param string $name
     * @return bool
     */
    public function existsByName(string $name): bool;
}
