<?php

namespace App\Http\Controllers;

use App\Http\Resources\MissionResource;
use App\Contracts\Services\MissionServiceInterface;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MissionController extends Controller
{
    /**
     * The mission service.
     *
     * @var \App\Contracts\Services\MissionServiceInterface
     */
    private MissionServiceInterface $missionService;

    /**
     * Create a new class instance.
     *
     * @param \App\Contracts\Services\MissionServiceInterface $missionService
     * @return void
     */
    public function __construct(MissionServiceInterface $missionService)
    {
        $this->missionService = $missionService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return MissionResource::collection(
            $this->missionService->allForUser(),
        );
    }

    /**
     * Complete some mission.
     *
     * @param mixed $id
     * @return void
     */
    public function complete(mixed $id): void
    {
        $this->missionService->complete($id);
    }
}
