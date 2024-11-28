<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use App\Models\{User, Transaction};
use App\Repositories\TransactionRepository;
use Illuminate\Database\Eloquent\Collection;
use App\Contracts\Services\TransactionServiceInterface;
use App\Exceptions\User\ResourceDoesntBelongsToUserException;

class TransactionServiceTest extends TestCase
{
    /**
     * The transaction service.
     *
     * @var \App\Contracts\Services\TransactionServiceInterface
     */
    private $transactionService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->transactionService = app(TransactionServiceInterface::class);
    }

    /**
     * Test if TransactionService uses the Transaction model correctly.
     *
     * @return void
     */
    public function test_transaction_repository_uses_transaction_model(): void
    {
        /** @var \App\Services\TransactionService $transactionService */
        $transactionService = $this->transactionService;

        $this->assertInstanceOf(TransactionRepository::class, $transactionService->repository());
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
        $mockService = Mockery::mock(TransactionServiceInterface::class);
        $mockService->shouldReceive('allForAuth')
            ->once()
            ->with($userMock)
            ->andReturn(Collection::make($transactionMock));

        /** @var \App\Models\User $userMock */
        /** @var \App\Contracts\Services\TransactionServiceInterface $mockService */
        $result = $mockService->allForAuth($userMock);

        $this->assertInstanceOf(Collection::class, $result);

        foreach ($result as $transaction) {
            /** @var \App\Models\Transaction $transaction */
            $this->assertEquals($transaction->id, $transactionMock->id);
        }
    }

    /**
     * Test if can remove transaction for given user.
     *
     * @return void
     */
    public function test_if_can_remove_transaction_for_given_user(): void
    {
        $userMock = Mockery::mock(User::class)->makePartial();
        $userMock->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $transactionMock = Mockery::mock(Transaction::class)->makePartial();
        $transactionMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $transactionMock->shouldReceive('getAttribute')->with('user_id')->andReturn(1);

        /** @var \App\Models\Transaction $transactionMock */
        $mockService = Mockery::mock(TransactionServiceInterface::class);
        $mockService->shouldReceive('deleteForUser')
            ->once()
            ->with($userMock, $transactionMock->id);

        /** @var \App\Models\User $userMock */
        /** @var \App\Contracts\Services\TransactionServiceInterface $mockService */
        $mockService->deleteForUser($userMock, $transactionMock->id);

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations match.');
    }

    /**
     * Test if can throw an exception on transaction delete if don't belongs to user.
     *
     * @return void
     */
    public function test_if_can_throw_an_exception_on_transaction_delete_if_dont_belongs_to_user(): void
    {
        $userMock = Mockery::mock(User::class)->makePartial();
        $userMock->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $transactionMock = Mockery::mock(Transaction::class)->makePartial();
        $transactionMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $transactionMock->shouldReceive('getAttribute')->with('user_id')->andReturn(2);

        /** @var \App\Models\Transaction $transactionMock */
        $mockService = Mockery::mock(TransactionServiceInterface::class);
        $mockService->shouldReceive('deleteForUser')
            ->once()
            ->with($userMock, $transactionMock->id)
            ->andThrow(ResourceDoesntBelongsToUserException::class);

        $this->expectException(ResourceDoesntBelongsToUserException::class);

        /** @var \App\Models\User $userMock */
        /** @var \App\Contracts\Services\TransactionServiceInterface $mockService */
        $mockService->deleteForUser($userMock, $transactionMock->id);
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
