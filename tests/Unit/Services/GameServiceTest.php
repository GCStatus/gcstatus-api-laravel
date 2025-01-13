<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Repositories\GameRepository;
use App\Contracts\Services\GameServiceInterface;
use App\Contracts\Repositories\GameRepositoryInterface;

class GameServiceTest extends TestCase
{
    /**
     * Test if GameService uses the User model correctly.
     *
     * @return void
     */
    public function test_user_repository_uses_user_model(): void
    {
        $this->app->instance(GameRepositoryInterface::class, new GameRepository());

        /** @var \App\Services\GameService $gameService */
        $gameService = app(GameServiceInterface::class);

        $this->assertInstanceOf(GameRepository::class, $gameService->repository());
    }
}
