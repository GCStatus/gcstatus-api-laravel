<?php

namespace App\Http\Controllers;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\{
    TitleResource,
    MissionResource,
};
use App\Contracts\Services\{
    MissionServiceInterface,
    TitleOwnershipServiceInterface,
};

class MissionController extends Controller
{
    /**
     * The mission service.
     *
     * @var \App\Contracts\Services\MissionServiceInterface
     */
    private MissionServiceInterface $missionService;

    /**
     * The title ownership service.
     *
     * @var \App\Contracts\Services\TitleOwnershipServiceInterface
     */
    private TitleOwnershipServiceInterface $titleOwnershipService;

    /**
     * Create a new class instance.
     *
     * @param \App\Contracts\Services\MissionServiceInterface $missionService
     * @param \App\Contracts\Services\TitleOwnershipServiceInterface $titleOwnershipService
     * @return void
     */
    public function __construct(
        MissionServiceInterface $missionService,
        TitleOwnershipServiceInterface $titleOwnershipService,
    ) {
        $this->missionService = $missionService;
        $this->titleOwnershipService = $titleOwnershipService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        TitleResource::setTitleOwnershipService($this->titleOwnershipService);

        return MissionResource::collection(
            $this->missionService->allForUser(),
        );
    }

    /**
     * Complete some mission.
     *
     * @param mixed $id
     * @return \App\Http\Resources\MissionResource
     */
    public function complete(mixed $id): void
    {
        $this->missionService->complete($id);
    }
}
