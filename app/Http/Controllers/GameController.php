<?php

namespace App\Http\Controllers;

use App\Http\Resources\GameResource;
use App\Contracts\Services\GameServiceInterface;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Requests\Game\{GameSearchRequest, GameFilterAttributeRequest};

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

    /**
     * Display the games for search.
     *
     * @param \App\Http\Requests\Game\GameSearchRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function search(GameSearchRequest $request): AnonymousResourceCollection
    {
        /** @var array<string, string> $data */
        $data = $request->validated();

        /** @var string $query */
        $query = $data['q'];

        return GameResource::collection(
            $this->gameService->search($query),
        );
    }

    /**
     * Find games by given condition.
     *
     * @param string $condition
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function findByCondition(string $condition): AnonymousResourceCollection
    {
        return GameResource::collection(
            $this->gameService->getGamesByCondition($condition, 100),
        );
    }

    /**
     * Find games by attributes.
     *
     * @param \App\Http\Requests\Game\GameFilterAttributeRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function findByFilters(GameFilterAttributeRequest $request): AnonymousResourceCollection
    {
        /** @var array<string, string> $data */
        $data = $request->validated();

        return GameResource::collection(
            $this->gameService->findByAttribute($data),
        );
    }
}
