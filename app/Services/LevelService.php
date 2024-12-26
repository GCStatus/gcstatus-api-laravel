<?php

namespace App\Services;

use App\Models\{User, Level};
use Illuminate\Database\Eloquent\Collection;
use App\Contracts\Repositories\LevelRepositoryInterface;
use App\Contracts\Services\{
    LevelServiceInterface,
    CacheServiceInterface,
    LevelNotificationServiceInterface,
};
use Illuminate\Support\Facades\DB;

class LevelService extends AbstractService implements LevelServiceInterface
{
    /**
     * The cache service.
     *
     * @var \App\Contracts\Services\CacheServiceInterface
     */
    private CacheServiceInterface $cacheService;

    /**
     * The level notification service.
     *
     * @var \App\Contracts\Services\LevelNotificationServiceInterface
     */
    private LevelNotificationServiceInterface $levelNotificationService;

    /**
     * The level repository.
     *
     * @return \App\Contracts\Repositories\LevelRepositoryInterface
     */
    public function repository(): LevelRepositoryInterface
    {
        return app(LevelRepositoryInterface::class);
    }

    /**
     * Create a new class instance.
     *
     * @param \App\Contracts\Services\CacheServiceInterface $cacheService
     * @return void
     */
    public function __construct(CacheServiceInterface $cacheService)
    {
        $this->cacheService = $cacheService;
        $this->levelNotificationService = app(LevelNotificationServiceInterface::class);
    }

    /**
     * @inheritDoc
     */
    public function all(): Collection
    {
        $key = 'gcstatus_levels_key';

        if (!$this->cacheService->has($key)) {
            $levels = $this->repository()->all();

            $this->cacheService->forever($key, $levels);

            return $levels;
        }

        /** @var \Illuminate\Database\Eloquent\Collection<int, \Illuminate\Database\Eloquent\Model> $levels */
        $levels = $this->cacheService->get($key);

        return $levels;
    }

    /**
     * @inheritDoc
     */
    public function handleLevelUp(User $user): void
    {
        /** @var \App\Models\Level $level */
        $level = $user->level;

        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Level> $levels */
        $levels = $this->repository()
            ->getLevelsAboveByLevel($level->level)
            ->load('rewards.rewardable');

        DB::transaction(function () use ($user, $levels) {
            foreach ($levels as $nextLevel) {
                if ($user->experience < $nextLevel->experience) {
                    break;
                }

                $user->experience -= $nextLevel->experience;
                $user->level_id = $nextLevel->id;

                $this->handleLevelUpRewards($user, $nextLevel);
            }

            $user->save();
        });
    }

    /**
     * Handle awards for user leveling up.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Level $level
     * @return void
     */
    private function handleLevelUpRewards(User $user, Level $level): void
    {
        if ($level->coins > 0) {
            awarder()->awardCoins($user, $level->coins, "You earned {$level->coins} for leveling up!");
        }

        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Rewardable> $rewards */
        $rewards = $level->rewards;

        if ($rewards->isNotEmpty()) {
            awarder()->awardRewards($user, $rewards);
        }

        $this->levelNotificationService->notifyLevelUp($user, $level);
    }
}
