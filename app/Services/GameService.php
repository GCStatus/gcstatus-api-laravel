<?php

namespace App\Services;

use App\Contracts\Services\GameServiceInterface;
use App\Contracts\Repositories\GameRepositoryInterface;

class GameService extends AbstractService implements GameServiceInterface
{
    /**
     * The game repository.
     *
     * @return \App\Contracts\Repositories\GameRepositoryInterface
     */
    public function repository(): GameRepositoryInterface
    {
        return app(GameRepositoryInterface::class);
    }
}
