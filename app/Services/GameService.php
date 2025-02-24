<?php

namespace App\Services;

use App\Models\{Game, Status};
use Illuminate\Database\Eloquent\Collection;
use App\Contracts\Services\GameServiceInterface;
use App\Contracts\Repositories\GameRepositoryInterface;

class GameService extends AbstractService implements GameServiceInterface
{
    /**
     * @inheritDoc
     */
    public function repository(): GameRepositoryInterface
    {
        return app(GameRepositoryInterface::class);
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): Game
    {
        $data['about'] = clean($data['about']);
        $data['description'] = clean($data['description']);

        /** @var \App\Models\Game */
        return $this->repository()->create($data);
    }

    /**
     * @inheritDoc
     */
    public function update(array $data, mixed $id): Game
    {
        if (isset($data['about'])) {
            $data['about'] = clean($data['about']);
        }

        if (isset($data['description'])) {
            $data['description'] = clean($data['description']);
        }

        /** @var \App\Models\Game $game */
        $game = $this->repository()->update($data, $id);

        $this->syncRelationships($game, $data);

        if ($crack = issetGetter($data, 'crack')) {
            $this->updateCrackInfo($game, $crack); // @phpstan-ignore-line
        }

        return $game;
    }

    /**
     * @inheritDoc
     */
    public function findByAttribute(array $data): Collection
    {
        return $this->repository()->findByAttribute($data);
    }

    /**
     * @inheritDoc
     */
    public function search(string $query): Collection
    {
        return $this->repository()->search($query);
    }

    /**
     * @inheritDoc
     */
    public function getCalendarGames(): Collection
    {
        return $this->repository()->getCalendarGames();
    }

    /**
     * @inheritDoc
     */
    public function details(string $slug): Game
    {
        $game = $this->repository()->details($slug);

        $this->createGameView($game);

        return $game;
    }

    /**
     * @inheritDoc
     */
    public function detailsForAdmin(mixed $id): Game
    {
        return $this->repository()->detailsForAdmin($id);
    }

    /**
     * @inheritDoc
     */
    public function getGamesByCondition(string $condition, int $limit): Collection
    {
        return $this->repository()->getGamesByCondition($condition, $limit);
    }

    /**
     * @inheritDoc
     */
    public function getUpcomingGames(int $limit): Collection
    {
        return $this->repository()->getUpcomingGames($limit);
    }

    /**
     * @inheritDoc
     */
    public function getMostLikedGames(int $limit): Collection
    {
        return $this->repository()->getMostLikedGames($limit);
    }

    /**
     * @inheritDoc
     */
    public function getNextGreatRelease(): ?Game
    {
        return $this->repository()->getNextGreatRelease();
    }

    /**
     * Create the related game view.
     *
     * @param \App\Models\Game $game
     * @return void
     */
    private function createGameView(Game $game): void
    {
        $game->increment('views');
    }

    /**
     * Sync the relations of game.
     *
     * @param \App\Models\Game $game
     * @param array<string, mixed> $data
     * @return void
     */
    private function syncRelationships(Game $game, array $data): void
    {
        $relations = [
            'tags',
            'genres',
            'platforms',
            'categories',
            'publishers',
            'developers',
        ];

        foreach ($relations as $relation) {
            if (array_key_exists($relation, $data)) {
                $game->{$relation}()->sync($data[$relation] ?? []);
            }
        }
    }

    /**
     * Update crack info of the game.
     *
     * @param \App\Models\Game $game
     * @param array<string, mixed> $crack
     * @return void
     */
    private function updateCrackInfo(Game $game, array $crack): void
    {
        $data = [
            'cracked_at' => $crack['cracked_at'] ?? null,
            'cracker_id' => $crack['cracker_id'] ?? null,
            'protection_id' => $crack['protection_id'] ?? null,
            'status_id' => isset($crack['status']) ? Status::TRANSLATE_TO_ID[$crack['status']] : null,
        ];

        $filteredData = array_filter($data, function (mixed $value) {
            return $value !== null;
        });

        $game->crack()->updateOrCreate([], $filteredData);
    }
}
