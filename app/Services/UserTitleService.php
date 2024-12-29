<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\{Status, User, Title, UserTitle};
use App\Exceptions\Title\TitleIsUnavailableException;
use App\Contracts\Repositories\UserTitleRepositoryInterface;
use App\Exceptions\UserTitle\{
    TitleIsntPurchasableException,
    UserAlreadyHasGivenUserTitleException,
};
use App\Contracts\Services\{
    AuthServiceInterface,
    TitleServiceInterface,
    WalletServiceInterface,
    UserTitleServiceInterface,
    TitleNotificationServiceInterface,
};

class UserTitleService extends AbstractService implements UserTitleServiceInterface
{
    /**
     * The auth service.
     *
     * @var \App\Contracts\Services\AuthServiceInterface
     */
    private AuthServiceInterface $authService;

    /**
     * The title service.
     *
     * @var \App\Contracts\Services\TitleServiceInterface
     */
    private TitleServiceInterface $titleService;

    /**
     * The wallet service.
     *
     * @var \App\Contracts\Services\WalletServiceInterface
     */
    private WalletServiceInterface $walletService;

    /**
     * The title notification service.
     *
     * @var \App\Contracts\Services\TitleNotificationServiceInterface
     */
    private TitleNotificationServiceInterface $titleNotificationService;

    /**
     * Get the repository instance.
     *
     * @return \App\Contracts\Repositories\UserTitleRepositoryInterface
     */
    public function repository(): UserTitleRepositoryInterface
    {
        return app(UserTitleRepositoryInterface::class);
    }

    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authService = app(AuthServiceInterface::class);
        $this->titleService = app(TitleServiceInterface::class);
        $this->walletService = app(WalletServiceInterface::class);
        $this->titleNotificationService = app(TitleNotificationServiceInterface::class);
    }

    /**
     * @inheritDoc
     */
    public function buyTitle(mixed $id): UserTitle
    {
        return DB::transaction(function () use ($id) {
            /** @var \App\Models\User $user */
            $user = $this->authService->getAuthUser();

            /** @var \App\Models\Title $title */
            $title = $this->titleService->findOrFail($id);

            $this->assertCanBuy($title);

            /** @var int $cost */
            $cost = $title->cost;

            $this->walletService->deductFunds(
                $user,
                $cost,
                "You bought the title {$title->title} for {$title->cost} coins!",
            );

            $result = $this->assignTitleToUser($user, $title);

            return $result;
        });
    }

    /**
     * @inheritDoc
     */
    public function assignTitleToUser(User $user, Title $title): UserTitle
    {
        return DB::transaction(function () use ($user, $title) {
            $this->assertCanAssignTitleToUser($user, $title);

            /** @var \App\Models\UserTitle $result */
            $result = $this->repository()->create([
                'user_id' => $user->id,
                'title_id' => $title->id,
            ]);

            $this->titleNotificationService->notifyNewTitle($user, $title);

            return $result;
        });
    }

    /**
     * @inheritDoc
     */
    public function toggle(mixed $id): void
    {
        /** @var int $userId */
        $userId = $this->authService->getAuthId();

        DB::transaction(function () use ($userId, $id) {
            $this->repository()->toggleTitle($userId, $id);
        });

        $this->removeUserCache($userId);
    }

    /**
     * Assert can assign title to user.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Title $title
     * @throws \App\Exceptions\UserTitle\UserAlreadyHasGivenUserTitleException
     * @return void
     */
    private function assertCanAssignTitleToUser(User $user, Title $title): void
    {
        $user->load('titles');

        if ($user->titles->contains('pivot.title_id', $title->id)) {
            throw new UserAlreadyHasGivenUserTitleException();
        }
    }

    /**
     * Assert can buy a title.
     *
     * @param \App\Models\Title $title
     * @throws \App\Exceptions\Title\TitleIsUnavailableException
     * @throws \App\Exceptions\UserTitle\TitleIsntPurchasableException
     * @return void
     */
    private function assertCanBuy(Title $title): void
    {
        if (in_array($title->status_id, [Status::UNAVAILABLE_STATUS_ID])) {
            throw new TitleIsUnavailableException();
        }

        if (!$title->purchasable || (!$title->cost || $title->cost <= 0)) {
            throw new TitleIsntPurchasableException();
        }
    }

    /**
     * Remove user cache after toggle title.
     *
     * @param int $userId
     * @return void
     */
    private function removeUserCache(int $userId): void
    {
        $key = "auth.user.{$userId}";

        cacher()->forget($key);
    }
}
