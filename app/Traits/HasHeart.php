<?php

namespace App\Traits;

use RuntimeException;
use App\Contracts\HasHeartInterface;
use App\Contracts\Services\AuthServiceInterface;
use Illuminate\Database\Eloquent\{Model, Builder};

/**
 * Automatically generates the is_hearted attribute for an Eloquent model.
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static> scopeWithIsHearted(\Illuminate\Database\Eloquent\Builder<static> $query)
 */
trait HasHeart
{
    /**
     * Boot the HasHeart trait.
     *
     * @return void
     */
    protected static function bootHasHeart(): void
    {
        static::retrieved(function (Model $model) {
            if (!$model instanceof HasHeartInterface) {
                throw new RuntimeException(sprintf(
                    'The model [%s] must implement HasHeartInterface to use the HasHeart trait.',
                    get_class($model),
                ));
            }
        });
    }

    /**
     * Get the is hearted builder query.
     *
     * @param \Illuminate\Database\Eloquent\Builder<static> $query
     * @return \Illuminate\Database\Eloquent\Builder<static>
     */
    public function scopeWithIsHearted(Builder $query): Builder
    {
        $authService = app(AuthServiceInterface::class);
        $userId = $authService->getAuthId();

        return $query->withCount([
            'hearts as is_hearted' => function (Builder $query) use ($userId) {
                $query->where('user_id', $userId);
            },
        ]);
    }
}
