<?php

namespace App\Http\Controllers;

use App\Http\Resources\TitleResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Contracts\Services\{
    TitleServiceInterface,
    TitleOwnershipServiceInterface,
};

class TitleController extends Controller
{
    /**
     * The title service.
     *
     * @var \App\Contracts\Services\TitleServiceInterface
     */
    private TitleServiceInterface $titleService;

    /**
     * The title ownership service.
     *
     * @var \App\Contracts\Services\TitleOwnershipServiceInterface
     */
    private TitleOwnershipServiceInterface $titleOwnershipService;

    /**
     * Create a new class instance.
     *
     * @param \App\Contracts\Services\TitleServiceInterface $titleService
     * @return void
     */
    public function __construct(
        TitleServiceInterface $titleService,
        TitleOwnershipServiceInterface $titleOwnershipService
    ) {
        $this->titleService = $titleService;
        $this->titleOwnershipService = $titleOwnershipService;
    }

    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function __invoke(): AnonymousResourceCollection
    {
        $titles = $this->titleService->allForUser();

        TitleResource::setTitleOwnershipService($this->titleOwnershipService);
        TitleResource::preloadOwnership($titles->toArray());

        return TitleResource::collection($titles);
    }
}
