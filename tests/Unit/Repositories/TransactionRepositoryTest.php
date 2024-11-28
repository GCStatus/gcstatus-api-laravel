<?php

namespace Tests\Unit\Repositories;

use Mockery;
use Tests\TestCase;
use App\Models\{User, Transaction};
use Illuminate\Database\Eloquent\Collection;
use App\Contracts\Repositories\TransactionRepositoryInterface;

class TransactionRepositoryTest extends TestCase
{
    /**
     * The transaction repository.
     *
     * @var \App\Contracts\Repositories\TransactionRepositoryInterface
     */
    private $transactionRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->transactionRepository = app(TransactionRepositoryInterface::class);
    }

    /**
     * Test if TransactionRepository uses the Transaction model correctly.
     *
     * @return void
     */
    public function test_transaction_repository_uses_transaction_model(): void
    {
        /** @var \App\Repositories\TransactionRepository $transactionRepository */
        $transactionRepository = $this->transactionRepository;

        $this->assertInstanceOf(Transaction::class, $transactionRepository->model());
    }

    /**
     * Test if can get all transactions for given user.
     *
     * @return void
     */
    public function test_if_can_get_all_transactions_for_given_user(): void
    {
        $userMock = Mockery::mock(User::class)->makePartial();
        $userMock->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $transactionMock = Mockery::mock(Transaction::class)->makePartial();
        $transactionMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $transactionMock->shouldReceive('getAttribute')->with('user_id')->andReturn(1);

        /** @var \App\Models\Transaction $transactionMock */
        $mockRepository = Mockery::mock(TransactionRepositoryInterface::class);
        $mockRepository->shouldReceive('allForAuth')
            ->once()
            ->with($userMock)
            ->andReturn(Collection::make($transactionMock));

        /** @var \App\Models\User $userMock */
        /** @var \App\Contracts\Repositories\TransactionRepositoryInterface $mockRepository */
        $result = $mockRepository->allForAuth($userMock);

        $this->assertInstanceOf(Collection::class, $result);

        foreach ($result as $transaction) {
            /** @var \App\Models\Transaction $transaction */
            $this->assertEquals($transaction->id, $transactionMock->id);
        }
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
