<?php

namespace App\Repositories;

use App\Models\Game;
use Illuminate\Database\Eloquent\{Builder, Collection};
use App\Contracts\Repositories\GameRepositoryInterface;
use Illuminate\Database\Eloquent\Relations\{HasMany, MorphMany};

class GameRepository extends AbstractRepository implements GameRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function model(): Game
    {
        return new Game();
    }

    /**
     * @inheritDoc
     */
    public function getCalendarGames(): Collection
    {
        return $this->model()
            ->query()
            ->select(
                'id',
                'slug',
                'title',
                'cover',
                'views',
                'condition',
                'release_date',
            )->withCount('hearts')
            ->withIsHearted()
            ->where(function (Builder $query) {
                $currentYear = now()->year;
                $lastYear = $currentYear - 1;

                $query->whereYear('release_date', $currentYear)->orWhereYear('release_date', $lastYear);
            })->get();
    }

    /**
     * @inheritDoc
     */
    public function details(string $slug): Game
    {
        return $this->model()
            ->query()
            ->withIsHearted()
            ->with([ // @phpstan-ignore-line
                'support',
                'dlcs.tags',
                'developers',
                'publishers',
                'dlcs.genres',
                'reviews.user',
                'stores.store',
                'critics.critic',
                'dlcs.platforms',
                'dlcs.categories',
                'dlcs.developers',
                'dlcs.publishers',
                'dlcs.stores.store',
                'torrents.provider',
                'languages.language',
                'galleries.mediaType',
                'dlcs.galleries.mediaType',
                'requirements.requirementType',
                'comments' => function (MorphMany $query) {
                    $query->withIsHearted()->with([ // @phpstan-ignore-line
                        'user',
                        'children' => function (HasMany $query) {
                            $query->withIsHearted()->with('user'); // @phpstan-ignore-line
                        }
                    ]);
                },
            ])->where('slug', $slug)->firstOrFail();
    }

    /**
     * @inheritDoc
     */
    public function getGamesByCondition(string $condition, int $limit = 100): Collection
    {
        return $this->model()
            ->query()
            ->where('condition', $condition)
            ->withIsHearted()
            ->limit($limit)
            ->get();
    }

    /**
     * @inheritDoc
     */
    public function getUpcomingGames(int $limit = 100): Collection
    {
        return $this->model()
            ->query()
            ->where('release_date', '>', today())
            ->withIsHearted()
            ->limit($limit)
            ->get();
    }

    /**
     * @inheritDoc
     */
    public function getMostLikedGames(int $limit = 100): Collection
    {
        return $this->model()
            ->query()
            ->orderByDesc('hearts_count')
            ->withIsHearted()
            ->limit($limit)
            ->get();
    }

    /**
     * @inheritDoc
     */
    public function getNextGreatRelease(): ?Game
    {
        return $this->model()
            ->query()
            ->where('great_release', true)
            ->where('release_date', '>=', today())
            ->first();
    }
}
