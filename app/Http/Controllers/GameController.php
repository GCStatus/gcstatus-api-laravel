<?php

namespace App\Http\Controllers;

use App\Http\Resources\GameResource;
use App\Contracts\Services\GameServiceInterface;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GameController extends Controller
{
    /**
     * The game service.
     *
     * @var \App\Contracts\Services\GameServiceInterface
     */
    private GameServiceInterface $gameService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Contracts\Services\GameServiceInterface $gameService
     * @return void
     */
    public function __construct(
        GameServiceInterface $gameService,
    ) {
        $this->gameService = $gameService;
    }

    /**
     * Display the specified resource.
     *
     * @param string $slug
     * @return \App\Http\Resources\GameResource
     */
    public function show(string $slug): GameResource
    {
        return GameResource::make(
            $this->gameService->details($slug),
        );
    }

    /**
     * Display the games calendar.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function calendar(): AnonymousResourceCollection
    {
        return GameResource::collection(
            $this->gameService->getCalendarGames(),
        );
    }
}
