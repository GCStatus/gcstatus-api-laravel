<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use Mockery\MockInterface;
use App\Services\UserTitleService;
use App\Repositories\UserTitleRepository;
use Illuminate\Database\Eloquent\Collection;
use App\Models\{Status, User, Title, UserTitle, Wallet};
use App\Contracts\Repositories\UserTitleRepositoryInterface;
use App\Exceptions\{
    Title\TitleIsUnavailableException,
    UserTitle\TitleIsntPurchasableException,
    UserTitle\UserAlreadyHasGivenUserTitleException,
};
use App\Contracts\Services\{
    AuthServiceInterface,
    TitleServiceInterface,
    WalletServiceInterface,
    UserTitleServiceInterface,
    TitleNotificationServiceInterface,
};

class UserTitleServiceTest extends TestCase
{
    /**
     * The auth service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $authService;

    /**
     * The title service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $titleService;

    /**
     * The wallet service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $walletService;

    /**
     * The user title service.
     *
     * @var \App\Contracts\Services\UserTitleServiceInterface
     */
    private UserTitleServiceInterface $userTitleService;

    /**
     * The title notification service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $titleNotificationService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->authService = Mockery::mock(AuthServiceInterface::class);
        $this->titleService = Mockery::mock(TitleServiceInterface::class);
        $this->walletService = Mockery::mock(WalletServiceInterface::class);
        $this->titleNotificationService = Mockery::mock(TitleNotificationServiceInterface::class);

        $this->app->instance(AuthServiceInterface::class, $this->authService);
        $this->app->instance(TitleServiceInterface::class, $this->titleService);
        $this->app->instance(WalletServiceInterface::class, $this->walletService);
        $this->app->instance(TitleNotificationServiceInterface::class, $this->titleNotificationService);

        $this->userTitleService = app(UserTitleServiceInterface::class);
    }

    /**
     * Test if UserTitleService uses the UserTitle model correctly.
     *
     * @return void
     */
    public function test_user_title_repository_uses_user_title_model(): void
    {
        /** @var \App\Services\UserTitleService $userTitleService */
        $userTitleService = $this->userTitleService;

        $this->assertInstanceOf(UserTitleRepository::class, $userTitleService->repository());
    }

    /**
     * Test if can't assign title to user and throws exception if user already has title.
     *
     * @return void
     */
    public function test_if_cant_assign_title_to_user_and_throws_exception_if_user_already_has_title(): void
    {
        $titleId = 1;

        $user = Mockery::mock(User::class);
        $title = Mockery::mock(Title::class);

        $title->shouldReceive('getAttribute')->with('id')->andReturn($titleId);

        $user->shouldReceive('load')
            ->once()
            ->with('titles')
            ->andReturnSelf();

        $user->shouldReceive('getAttribute')
            ->with('titles')
            ->andReturn(Collection::make([
                (object)['pivot' => (object)['title_id' => $titleId]],
            ]));

        $this->expectException(UserAlreadyHasGivenUserTitleException::class);
        $this->expectExceptionMessage('The user already has the given title.');

        /** @var \App\Models\User $user */
        /** @var \App\Models\Title $title */
        $this->userTitleService->assignTitleToUser($user, $title);
    }

    /**
     * Test if can assign title to user if not exists.
     *
     * @return void
     */
    public function test_if_can_assign_title_to_user_if_not_exists(): void
    {
        $userId = 1;
        $titleId = 2;

        $user = Mockery::mock(User::class);
        $title = Mockery::mock(Title::class);
        $repository = Mockery::mock(UserTitleRepositoryInterface::class);
        $service = Mockery::mock(UserTitleService::class, [])->makePartial();

        $user->shouldReceive('getAttribute')->with('id')->andReturn($userId);
        $title->shouldReceive('getAttribute')->with('id')->andReturn($titleId);

        $user->shouldReceive('load')
            ->once()
            ->with('titles')
            ->andReturnSelf();

        $user->shouldReceive('getAttribute')
            ->with('titles')
            ->andReturn(Collection::make());

        $this->titleNotificationService
            ->shouldReceive('notifyNewTitle')
            ->once()
            ->with($user, $title);

        $service->shouldReceive('repository')->andReturn($repository);

        $repository->shouldReceive('create')
            ->with(['user_id' => $userId, 'title_id' => $titleId])
            ->andReturn(Mockery::mock(UserTitle::class));

        /** @var \App\Models\User $user */
        /** @var \App\Models\Title $title */
        /** @var \App\Contracts\Services\UserTitleServiceInterface $service */
        $result = $service->assignTitleToUser($user, $title);

        $this->assertInstanceOf(UserTitle::class, $result);
    }

    /**
     * Test if can buy a title.
     *
     * @return void
     */
    public function test_if_can_buy_a_title(): void
    {
        $userId = 1;
        $titleId = 1;

        $user = Mockery::mock(User::class);
        $title = Mockery::mock(Title::class);
        $wallet = Mockery::mock(Wallet::class);
        $repository = Mockery::mock(UserTitleRepositoryInterface::class);
        $service = Mockery::mock(UserTitleService::class, [])->makePartial();

        $user->shouldReceive('getAttribute')->with('id')->andReturn($userId);
        $user->shouldReceive('getAttribute')->with('wallet')->andReturn($wallet);
        $user->shouldReceive('load')
            ->once()
            ->with('titles')
            ->andReturnSelf();
        $user->shouldReceive('getAttribute')
            ->with('titles')
            ->andReturn(Collection::make());

        $title->shouldReceive('getAttribute')->with('id')->andReturn($titleId);
        $title->shouldReceive('getAttribute')->with('purchasable')->andReturnTrue();
        $title->shouldReceive('getAttribute')->with('title')->andReturn(fake()->title());
        $title->shouldReceive('getAttribute')->with('cost')->andReturn(fake()->numberBetween(1, 100));
        $title->shouldReceive('getAttribute')->with('status_id')->andReturn(Status::AVAILABLE_STATUS_ID);

        $service->shouldReceive('repository')->andReturn($repository);

        $this->authService
            ->shouldReceive('getAuthUser')
            ->once()
            ->withNoArgs()
            ->andReturn($user);

        $this->titleService
            ->shouldReceive('findOrFail')
            ->once()
            ->with($titleId)
            ->andReturn($title);

        $this->titleNotificationService
            ->shouldReceive('notifyNewTitle')
            ->once()
            ->with($user, $title);

        /** @var \App\Models\Title $title */
        $this->walletService
            ->shouldReceive('deductFunds')
            ->once()
            ->with(
                $user,
                $title->cost,
                "You bought the title {$title->title} for {$title->cost} coins!",
            );

        $repository->shouldReceive('create')
            ->with(['user_id' => $userId, 'title_id' => $titleId])
            ->andReturn(Mockery::mock(UserTitle::class));

        /** @var \App\Contracts\Services\UserTitleServiceInterface $service */
        $result = $service->buyTitle($titleId);

        $this->assertInstanceOf(UserTitle::class, $result);

        $this->assertEquals(5, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can't buy a title if title is unavailable.
     *
     * @return void
     */
    public function test_if_cant_buy_a_title_if_title_is_unavailable(): void
    {
        $userId = 1;
        $titleId = 1;

        $user = Mockery::mock(User::class);
        $title = Mockery::mock(Title::class);
        $wallet = Mockery::mock(Wallet::class);
        $repository = Mockery::mock(UserTitleRepositoryInterface::class);
        $service = Mockery::mock(UserTitleService::class, [])->makePartial();

        $user->shouldReceive('getAttribute')->with('id')->andReturn($userId);
        $user->shouldReceive('getAttribute')->with('wallet')->andReturn($wallet);

        $title->shouldReceive('getAttribute')->with('id')->andReturn($titleId);
        $title->shouldReceive('getAttribute')->with('purchasable')->andReturnTrue();
        $title->shouldReceive('getAttribute')->with('title')->andReturn(fake()->title());
        $title->shouldReceive('getAttribute')->with('cost')->andReturn(fake()->numberBetween(1, 100));
        $title->shouldReceive('getAttribute')->with('status_id')->andReturn(Status::UNAVAILABLE_STATUS_ID);

        $service->shouldReceive('repository')->andReturn($repository);

        $this->authService
            ->shouldReceive('getAuthUser')
            ->once()
            ->withNoArgs()
            ->andReturn($user);

        $this->titleService
            ->shouldReceive('findOrFail')
            ->once()
            ->with($titleId)
            ->andReturn($title);

        $this->titleNotificationService->shouldNotReceive('notifyNewTitle');

        $this->walletService->shouldNotReceive('deductFunds');

        $repository->shouldNotReceive('create');

        $this->expectException(TitleIsUnavailableException::class);
        $this->expectExceptionMessage('The given title is unavailable!');

        /** @var \App\Contracts\Services\UserTitleServiceInterface $service */
        $service->buyTitle($titleId);

        $this->assertEquals(5, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can't buy a title if title is not purchasable.
     *
     * @return void
     */
    public function test_if_cant_buy_a_title_if_title_is_not_purchasable(): void
    {
        $userId = 1;
        $titleId = 1;

        $user = Mockery::mock(User::class);
        $title = Mockery::mock(Title::class);
        $wallet = Mockery::mock(Wallet::class);
        $repository = Mockery::mock(UserTitleRepositoryInterface::class);
        $service = Mockery::mock(UserTitleService::class, [])->makePartial();

        $user->shouldReceive('getAttribute')->with('id')->andReturn($userId);
        $user->shouldReceive('getAttribute')->with('wallet')->andReturn($wallet);

        $title->shouldReceive('getAttribute')->with('id')->andReturn($titleId);
        $title->shouldReceive('getAttribute')->with('purchasable')->andReturnFalse();
        $title->shouldReceive('getAttribute')->with('title')->andReturn(fake()->title());
        $title->shouldReceive('getAttribute')->with('cost')->andReturn(fake()->numberBetween(1, 100));
        $title->shouldReceive('getAttribute')->with('status_id')->andReturn(Status::AVAILABLE_STATUS_ID);

        $service->shouldReceive('repository')->andReturn($repository);

        $this->authService
            ->shouldReceive('getAuthUser')
            ->once()
            ->withNoArgs()
            ->andReturn($user);

        $this->titleService
            ->shouldReceive('findOrFail')
            ->once()
            ->with($titleId)
            ->andReturn($title);

        $this->titleNotificationService->shouldNotReceive('notifyNewTitle');

        $this->walletService->shouldNotReceive('deductFunds');

        $repository->shouldNotReceive('create');

        $this->expectException(TitleIsntPurchasableException::class);
        $this->expectExceptionMessage('The given title is not purchasable!');

        /** @var \App\Contracts\Services\UserTitleServiceInterface $service */
        $service->buyTitle($titleId);

        $this->assertEquals(5, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can't buy a title if title is has no price.
     *
     * @return void
     */
    public function test_if_cant_buy_a_title_if_title_is_has_no_price(): void
    {
        $userId = 1;
        $titleId = 1;

        $user = Mockery::mock(User::class);
        $title = Mockery::mock(Title::class);
        $wallet = Mockery::mock(Wallet::class);
        $repository = Mockery::mock(UserTitleRepositoryInterface::class);
        $service = Mockery::mock(UserTitleService::class, [])->makePartial();

        $user->shouldReceive('getAttribute')->with('id')->andReturn($userId);
        $user->shouldReceive('getAttribute')->with('wallet')->andReturn($wallet);

        $title->shouldReceive('getAttribute')->with('cost')->andReturn(0);
        $title->shouldReceive('getAttribute')->with('id')->andReturn($titleId);
        $title->shouldReceive('getAttribute')->with('purchasable')->andReturnTrue();
        $title->shouldReceive('getAttribute')->with('title')->andReturn(fake()->title());
        $title->shouldReceive('getAttribute')->with('status_id')->andReturn(Status::AVAILABLE_STATUS_ID);

        $service->shouldReceive('repository')->andReturn($repository);

        $this->authService
            ->shouldReceive('getAuthUser')
            ->once()
            ->withNoArgs()
            ->andReturn($user);

        $this->titleService
            ->shouldReceive('findOrFail')
            ->once()
            ->with($titleId)
            ->andReturn($title);

        $this->titleNotificationService->shouldNotReceive('notifyNewTitle');

        $this->walletService->shouldNotReceive('deductFunds');

        $repository->shouldNotReceive('create');

        $this->expectException(TitleIsntPurchasableException::class);
        $this->expectExceptionMessage('The given title is not purchasable!');

        /** @var \App\Contracts\Services\UserTitleServiceInterface $service */
        $service->buyTitle($titleId);

        $this->assertEquals(5, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can't buy a title if user already has given title.
     *
     * @return void
     */
    public function test_if_cant_buy_a_title_if_user_already_has_given_title(): void
    {
        $userId = 1;
        $titleId = 1;

        $user = Mockery::mock(User::class);
        $title = Mockery::mock(Title::class);
        $wallet = Mockery::mock(Wallet::class);
        $repository = Mockery::mock(UserTitleRepositoryInterface::class);
        $service = Mockery::mock(UserTitleService::class, [])->makePartial();

        $user->shouldReceive('getAttribute')->with('id')->andReturn($userId);
        $user->shouldReceive('getAttribute')->with('wallet')->andReturn($wallet);

        $user->shouldReceive('load')
            ->once()
            ->with('titles')
            ->andReturnSelf();

        $user->shouldReceive('getAttribute')
            ->with('titles')
            ->andReturn(Collection::make([
                (object)['pivot' => (object)['title_id' => $titleId]],
            ]));

        $title->shouldReceive('getAttribute')->with('id')->andReturn($titleId);
        $title->shouldReceive('getAttribute')->with('purchasable')->andReturnTrue();
        $title->shouldReceive('getAttribute')->with('title')->andReturn(fake()->title());
        $title->shouldReceive('getAttribute')->with('cost')->andReturn(fake()->numberBetween(1, 100));
        $title->shouldReceive('getAttribute')->with('status_id')->andReturn(Status::AVAILABLE_STATUS_ID);

        $service->shouldReceive('repository')->andReturn($repository);

        $this->authService
            ->shouldReceive('getAuthUser')
            ->once()
            ->withNoArgs()
            ->andReturn($user);

        $this->titleService
            ->shouldReceive('findOrFail')
            ->once()
            ->with($titleId)
            ->andReturn($title);

        /** @var \App\Models\Title $title */
        $this->walletService
            ->shouldReceive('deductFunds')
            ->once()
            ->with(
                $user,
                $title->cost,
                "You bought the title {$title->title} for {$title->cost} coins!",
            );

        $this->titleNotificationService->shouldNotReceive('notifyNewTitle');

        $repository->shouldNotReceive('create');

        $this->expectException(UserAlreadyHasGivenUserTitleException::class);
        $this->expectExceptionMessage('The user already has the given title.');

        /** @var \App\Contracts\Services\UserTitleServiceInterface $service */
        $service->buyTitle($titleId);

        $this->assertEquals(5, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Cleanup Mockery after each test.
     *
     * @return void
     */
    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
