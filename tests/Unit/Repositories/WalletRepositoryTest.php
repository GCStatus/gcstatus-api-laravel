<?php

namespace Tests\Unit\Repositories;

use Mockery;
use Tests\TestCase;
use App\Models\Wallet;
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
        $walletRepository = Mockery::mock(WalletRepositoryInterface::class);

        $wallet->shouldAllowMockingProtectedMethods();
        $wallet->shouldReceive('increment')
            ->once()
            ->with('balance', $amount)
            ->andReturnTrue();

        $wallet->shouldReceive('getAttribute')->with('id')->andReturn(1);

        /** @var \App\Models\Wallet $wallet */
        $walletRepository
            ->shouldReceive('findOrFail')
            ->once()
            ->with($wallet->id)
            ->andReturn($wallet);

        $walletRepository
            ->shouldReceive('increment')
            ->once()
            ->with($wallet->id, $amount)
            ->andReturnUsing(function (mixed $id, int $amount) use ($walletRepository) {
                /** @var \App\Contracts\Repositories\WalletRepositoryInterface $walletRepository */
                return $walletRepository->findOrFail($id)->increment('balance', $amount);
            });

        /** @var \App\Contracts\Repositories\WalletRepositoryInterface $walletRepository */
        $walletRepository->increment($wallet->id, $amount);

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

        $wallet = Mockery::mock(Wallet::class);
        $walletRepository = Mockery::mock(WalletRepositoryInterface::class);

        $wallet->shouldAllowMockingProtectedMethods();
        $wallet->shouldReceive('decrement')
            ->once()
            ->with('balance', $amount)
            ->andReturnTrue();

        $wallet->shouldReceive('getAttribute')->with('id')->andReturn(1);

        /** @var \App\Models\Wallet $wallet */
        $walletRepository
            ->shouldReceive('findOrFail')
            ->once()
            ->with($wallet->id)
            ->andReturn($wallet);

        $walletRepository
            ->shouldReceive('decrement')
            ->once()
            ->with($wallet->id, $amount)
            ->andReturnUsing(function (mixed $id, int $amount) use ($walletRepository) {
                /** @var \App\Contracts\Repositories\WalletRepositoryInterface $walletRepository */
                return $walletRepository->findOrFail($id)->decrement('balance', $amount);
            });

        /** @var \App\Contracts\Repositories\WalletRepositoryInterface $walletRepository */
        $walletRepository->decrement($wallet->id, $amount);

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
