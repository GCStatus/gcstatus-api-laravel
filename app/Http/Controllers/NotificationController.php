<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use App\Contracts\Services\NotificationServiceInterface;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NotificationController extends Controller
{
    /**
     * The notification service.
     *
     * @var \App\Contracts\Services\NotificationServiceInterface
     */
    private NotificationServiceInterface $notificationService;

    /**
     * Create a new class instance.
     *
     * @param \App\Contracts\Services\NotificationServiceInterface $notificationService
     * @return void
     */
    public function __construct(NotificationServiceInterface $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return NotificationResource::collection(
            $this->notificationService->all(),
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id
     * @return void
     */
    public function destroy(string $id): void
    {
        $this->notificationService->remove($id);
    }
}
