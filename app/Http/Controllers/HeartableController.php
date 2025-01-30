<?php

namespace App\Http\Controllers;

use App\Contracts\Services\HeartableServiceInterface;
use App\Http\Requests\Heartable\HeartableToggleRequest;

class HeartableController extends Controller
{
    /**
     * The heartable service.
     *
     * @var \App\Contracts\Services\HeartableServiceInterface
     */
    private HeartableServiceInterface $heartableService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Contracts\Services\HeartableServiceInterface $heartableService
     * @return void
     */
    public function __construct(HeartableServiceInterface $heartableService)
    {
        $this->heartableService = $heartableService;
    }

    /**
     * Handle the incoming request.
     *
     * @param \App\Http\Requests\Heartable\HeartableToggleRequest $request
     * @return void
     */
    public function __invoke(HeartableToggleRequest $request): void
    {
        /** @var array<string, mixed> $data */
        $data = $request->validated();

        $this->heartableService->toggle($data);
    }
}
