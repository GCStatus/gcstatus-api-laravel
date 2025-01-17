<?php

namespace App\Http\Controllers;

use App\Models\{Game, Banner};
use Illuminate\Http\JsonResponse;
use App\Contracts\Responses\ApiResponseInterface;
use App\Http\Resources\{GameResource, BannerResource};
use App\Contracts\Services\{BannerServiceInterface, GameServiceInterface};

class HomeController extends Controller
{
    /**
     * The game service.
     *
     * @var \App\Contracts\Services\GameServiceInterface
     */
    private GameServiceInterface $gameService;

    /**
     * The banner service.
     *
     * @var \App\Contracts\Services\BannerServiceInterface
     */
    private BannerServiceInterface $bannerService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Contracts\Services\GameServiceInterface $gameService
     * @param  \App\Contracts\Services\BannerServiceInterface $bannerService
     * @return void
     */
    public function __construct(
        GameServiceInterface $gameService,
        BannerServiceInterface $bannerService,
    ) {
        $this->gameService = $gameService;
        $this->bannerService = $bannerService;
    }

    /**
     * Handle the incoming request.
     *
     * @param \App\Contracts\Responses\ApiResponseInterface $response
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(ApiResponseInterface $response): JsonResponse
    {
        $upcomingGames = $this->gameService->getUpcomingGames(9);
        $nextRelease = $this->gameService->getNextGreatRelease();
        $mostLikedGames = $this->gameService->getMostLikedGames(9);
        $hotGames = $this->gameService->getGamesByCondition(Game::HOT_CONDITION, 9);
        $popularGames = $this->gameService->getGamesByCondition(Game::POPULAR_CONDITION, 9);
        $banners = $this->bannerService->allBasedOnComponent(Banner::HOME_HEADER_CAROUSEL_BANNERS);

        $data = [
            'hot' => GameResource::collection($hotGames),
            'banners' => BannerResource::collection($banners),
            'popular' => GameResource::collection($popularGames),
            'upcoming' => GameResource::collection($upcomingGames),
            'most_liked' => GameResource::collection($mostLikedGames),
            'next_release' => $nextRelease ? GameResource::make($nextRelease) : null,
        ];

        return response()->json(
            $response->setContent($data)->toArray(),
        );
    }
}
