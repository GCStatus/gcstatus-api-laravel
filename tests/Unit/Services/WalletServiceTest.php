<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use Mockery\MockInterface;
use App\Models\{TransactionType, User, Wallet};
use App\Contracts\Services\WalletServiceInterface;
use App\Contracts\Repositories\WalletRepositoryInterface;
use App\Contracts\Services\TransactionServiceInterface;

class WalletServiceTest extends TestCase
{
    /**
     * The wallet repository mock.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $walletRepository;

    /**
     * The transaction service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $transactionService;

    /**
     * The wallet service.
     *
     * @var \App\Contracts\Services\WalletServiceInterface
     */
    private WalletServiceInterface $walletService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->walletRepository = Mockery::mock(WalletRepositoryInterface::class);
        $this->transactionService = Mockery::mock(TransactionServiceInterface::class);

        $this->app->instance(WalletRepositoryInterface::class, $this->walletRepository);
        $this->app->instance(TransactionServiceInterface::class, $this->transactionService);

        $this->walletService = app(WalletServiceInterface::class);
    }

    /**
     * Test if WalletService uses the Wallet model correctly.
     *
     * @return void
     */
    public function test_wallet_repository_uses_wallet_model(): void
    {
        /** @var \App\Services\WalletService $walletService */
        $walletService = $this->walletService;

        $this->assertInstanceOf(WalletRepositoryInterface::class, $walletService->repository());
    }

    /**
     * Test if can increment the wallet balance amount.
     *
     * @return void
     */
    public function test_if_can_increment_the_wallet_balance_amount(): void
    {
        $amount = 100;
        $description = fake()->text();

        $user = Mockery::mock(User::class);
        $wallet = Mockery::mock(Wallet::class);

        $user->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $wallet->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $wallet->shouldReceive('getAttribute')->with('user')->andReturn($user);

        /** @var \App\Models\User $user */
        $this->walletRepository
            ->shouldReceive('findBy')
            ->once()
            ->with('user_id', $user->id)
            ->andReturn($wallet);

        $this->walletRepository
            ->shouldReceive('increment')
            ->once()
            ->with($wallet, $amount);

        $this->transactionService
            ->shouldReceive('create')
            ->once()
            ->with([
                'amount' => $amount,
                'user_id' => $user->id,
                'description' => $description,
                'transaction_type_id' => TransactionType::ADDITION_TYPE_ID,
            ]);

        $this->walletService->addFunds($user, $amount, $description);

        $this->assertEquals(3, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations meet.');
    }

    /**
     * Test if can decrement from the wallet balance amount.
     *
     * @return void
     */
    public function test_if_can_decrement_from_the_wallet_balance_amount(): void
    {
        $amount = 100;
        $description = fake()->text();

        $user = Mockery::mock(User::class);
        $wallet = Mockery::mock(Wallet::class);

        $user->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $wallet->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $wallet->shouldReceive('getAttribute')->with('user')->andReturn($user);

        /** @var \App\Models\User $user */
        $this->walletRepository
            ->shouldReceive('findBy')
            ->once()
            ->with('user_id', $user->id)
            ->andReturn($wallet);

        $this->walletRepository
            ->shouldReceive('decrement')
            ->once()
            ->with($wallet, $amount);

        $this->transactionService
            ->shouldReceive('create')
            ->once()
            ->with([
                'amount' => $amount,
                'user_id' => $user->id,
                'description' => $description,
                'transaction_type_id' => TransactionType::SUBTRACTION_TYPE_ID,
            ]);

        $this->walletService->deductFunds($user, $amount, $description);

        $this->assertEquals(3, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations meet.');
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
