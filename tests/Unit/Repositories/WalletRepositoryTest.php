<?php

namespace Tests\Unit\Repositories;

use Mockery;
use Tests\TestCase;
use App\Models\Wallet;
use App\Repositories\WalletRepository;
use App\Contracts\Repositories\WalletRepositoryInterface;

class WalletRepositoryTest extends TestCase
{
    /**
     * The wallet repository.
     *
     * @var \App\Contracts\Repositories\WalletRepositoryInterface
     */
    private $walletRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->walletRepository = app(WalletRepositoryInterface::class);
    }

    /**
     * Test if WalletRepository uses the Wallet model correctly.
     *
     * @return void
     */
    public function test_wallet_repository_uses_wallet_model(): void
    {
        /** @var \App\Repositories\WalletRepository $walletRepository */
        $walletRepository = $this->walletRepository;

        $this->assertInstanceOf(Wallet::class, $walletRepository->model());
    }

    /**
     * Test if can increment the wallet balance amount.
     *
     * @return void
     */
    public function test_if_can_increment_the_wallet_balance_amount(): void
    {
        $amount = 100;

        $wallet = Mockery::mock(Wallet::class);
        $wallet->shouldAllowMockingProtectedMethods();
        $wallet->shouldReceive('increment')
            ->once()
            ->with('balance', $amount)
            ->andReturnTrue();

        $walletRepository = Mockery::mock(WalletRepository::class)->makePartial();

        $walletRepository->shouldReceive('findOrFail')
            ->once()
            ->with(1)
            ->andReturn($wallet);

        /** @var \App\Contracts\Repositories\WalletRepositoryInterface $walletRepository */
        $walletRepository->increment(1, $amount);

        $this->assertEquals(2, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations meet.');
    }

    /**
     * Test if can decrement from the wallet balance amount.
     *
     * @return void
     */
    public function test_if_can_decrement_from_the_wallet_balance_amount(): void
    {
        $amount = 100;

        $wallet = Mockery::mock(Wallet::class);
        $wallet->shouldAllowMockingProtectedMethods();
        $wallet->shouldReceive('decrement')
            ->once()
            ->with('balance', $amount)
            ->andReturnTrue();

        $walletRepository = Mockery::mock(WalletRepository::class)->makePartial();

        $walletRepository->shouldReceive('findOrFail')
            ->once()
            ->with(1)
            ->andReturn($wallet);

        /** @var \App\Contracts\Repositories\WalletRepositoryInterface $walletRepository */
        $walletRepository->decrement(1, $amount);

        $this->assertEquals(2, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations meet.');
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
