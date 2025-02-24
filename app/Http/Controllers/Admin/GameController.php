<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\GameResource;
use App\Contracts\Services\GameServiceInterface;
use Illuminate\Routing\Controllers\{Middleware, HasMiddleware};
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Requests\Admin\Game\{GameStoreRequest, GameUpdateRequest};

class GameController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     *
     * @return array<int, mixed>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('scopes:view:games'),
            new Middleware('scopes:create:games', only: ['store']),
            new Middleware('scopes:update:games', only: ['update']),
            new Middleware('scopes:delete:games', only: ['destroy']),
        ];
    }

    /**
     * The game service.
     *
     * @var \App\Contracts\Services\GameServiceInterface
     */
    private GameServiceInterface $gameService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Contracts\Services\GameServiceInterface $gameService
     * @return void
     */
    public function __construct(GameServiceInterface $gameService)
    {
        $this->gameService = $gameService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return GameResource::collection(
            $this->gameService->all(),
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Admin\Game\GameStoreRequest $request
     * @return \App\Http\Resources\Admin\GameResource
     */
    public function store(GameStoreRequest $request): GameResource
    {
        try {
            /** @var array<string, mixed> $data */
            $data = $request->validated();

            $game = $this->gameService->create($data);

            return GameResource::make($game);
        } catch (Exception $e) {
            $this->handleDefaultExceptionLog('Failed to create a new game.', $e);

            throw $e;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param mixed $id
     * @return \App\Http\Resources\Admin\GameResource
     */
    public function show(mixed $id): GameResource
    {
        return GameResource::make(
            $this->gameService->detailsForAdmin($id),
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Admin\Game\GameUpdateRequest $request
     * @param mixed $id
     * @return \App\Http\Resources\Admin\GameResource
     */
    public function update(GameUpdateRequest $request, mixed $id): GameResource
    {
        try {
            /** @var array<string, mixed> $data */
            $data = $request->validated();

            $game = $this->gameService->update($data, $id);

            return GameResource::make($game);
        } catch (Exception $e) {
            $this->handleDefaultExceptionLog('Failed to update a game.', $e);

            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param mixed $id
     * @return void
     */
    public function destroy(mixed $id): void
    {
        $this->gameService->delete($id);
    }
}
