<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\Game;
use App\Contracts\Repositories\GameRepositoryInterface;

class GameRepositoryTest extends TestCase
{
    /**
     * The game repository.
     *
     * @var \App\Contracts\Repositories\GameRepositoryInterface
     */
    private GameRepositoryInterface $gameRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->gameRepository = app(GameRepositoryInterface::class);
    }

    /**
     * Test if GameRepository uses the Game model correctly.
     *
     * @return void
     */
    public function test_game_repository_uses_game_model(): void
    {
        /** @var \App\Repositories\GameRepository $gameRepository */
        $gameRepository = $this->gameRepository;

        $this->assertInstanceOf(Game::class, $gameRepository->model());
    }
}
