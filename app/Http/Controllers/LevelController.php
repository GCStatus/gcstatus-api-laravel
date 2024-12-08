<?php

namespace App\Http\Controllers;

use App\Http\Resources\LevelResource;
use App\Contracts\Services\LevelServiceInterface;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LevelController extends Controller
{
    /**
     * The level service.
     *
     * @var \App\Contracts\Services\LevelServiceInterface
     */
    private LevelServiceInterface $levelService;

    /**
     * Create a new class instance.
     *
     * @param \App\Contracts\Services\LevelServiceInterface $levelService
     * @return void
     */
    public function __construct(LevelServiceInterface $levelService)
    {
        $this->levelService = $levelService;
    }

    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function __invoke(): AnonymousResourceCollection
    {
        return LevelResource::collection(
            $this->levelService->all(),
        );
    }
}
