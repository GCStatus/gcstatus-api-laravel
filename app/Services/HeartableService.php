<?php

namespace App\Services;

use App\Models\Heartable;
use App\Contracts\Repositories\HeartableRepositoryInterface;
use App\Contracts\Services\{
    AuthServiceInterface,
    HeartableServiceInterface,
};

class HeartableService extends AbstractService implements HeartableServiceInterface
{
    /**
     * The auth service.
     *
     * @var \App\Contracts\Services\AuthServiceInterface
     */
    private AuthServiceInterface $authService;

    /**
     * Create a new service instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authService = app(AuthServiceInterface::class);
    }

    /**
     * @inheritDoc
     */
    public function repository(): HeartableRepositoryInterface
    {
        return app(HeartableRepositoryInterface::class);
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): Heartable
    {
        $userId = $this->authService->getAuthId();

        /** @var \App\Models\Heartable */
        return $this->repository()->create($data + [
            'user_id' => $userId,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function toggle(array $data): void
    {
        $repository = $this->repository();

        $userId = $this->authService->getAuthId();

        if ($heartable = $repository->findByUser($userId, $data)) {
            $this->delete($heartable->id);
            return;
        }

        $this->create($data);
    }
}
