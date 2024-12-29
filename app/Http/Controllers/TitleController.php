<?php

namespace App\Http\Controllers;

use App\Http\Resources\TitleResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Contracts\Services\{
    TitleServiceInterface,
    UserTitleServiceInterface,
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
     * The user title service.
     *
     * @var \App\Contracts\Services\UserTitleServiceInterface
     */
    private UserTitleServiceInterface $userTitleService;

    /**
     * Create a new class instance.
     *
     * @param \App\Contracts\Services\TitleServiceInterface $titleService
     * @param \App\Contracts\Services\UserTitleServiceInterface $userTitleService
     * @return void
     */
    public function __construct(
        TitleServiceInterface $titleService,
        UserTitleServiceInterface $userTitleService,
    ) {
        $this->titleService = $titleService;
        $this->userTitleService = $userTitleService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $titles = $this->titleService->allForUser();

        TitleResource::preloadOwnership($titles->toArray());

        return TitleResource::collection($titles);
    }

    /**
     * Buy a given title.
     *
     * @param mixed $id
     * @return \App\Http\Resources\TitleResource
     */
    public function buy(mixed $id): TitleResource
    {
        $userTitle = $this->userTitleService->buyTitle($id);

        /** @var \App\Models\Title $title */
        $title = $userTitle->title;

        return TitleResource::make($title);
    }

    /**
     * Enable or disable a given title.
     *
     * @param mixed $id
     * @return void
     */
    public function toggle(mixed $id): void
    {
        $this->userTitleService->toggle($id);
    }
}
