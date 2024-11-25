<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use App\Contracts\Repositories\LevelRepositoryInterface;
use App\Contracts\Services\{LevelServiceInterface, CacheServiceInterface};

class LevelService extends AbstractService implements LevelServiceInterface
{
    /**
     * The cache service.
     *
     * @var \App\Contracts\Services\CacheServiceInterface
     */
    private CacheServiceInterface $cacheService;

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
    }

    /**
     * Get all cached levels.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, \Illuminate\Database\Eloquent\Model>
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
}
