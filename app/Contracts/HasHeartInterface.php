<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Interface for models that have a "hearts" relationship.
 *
 * @template TModel of Model
 */
interface HasHeartInterface
{
    /**
     * Define the hearts relationship.
     *
     * @return MorphMany<\App\Models\Heartable, TModel>
     */
    public function hearts(): MorphMany;
}
