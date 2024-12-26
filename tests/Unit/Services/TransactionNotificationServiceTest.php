<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use App\Models\{Transaction, User};
use App\Notifications\DatabaseNotification;
use App\Contracts\Services\TransactionNotificationServiceInterface;

class TransactionNotificationServiceTest extends TestCase
{
    /**
     * The transaction notification service.
     *
     * @var \App\Contracts\Services\TransactionNotificationServiceInterface
     */
    private TransactionNotificationServiceInterface $transactionNotificationService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->transactionNotificationService = app(TransactionNotificationServiceInterface::class);
    }

    /**
     * Test if can notify a transaction for given user.
     *
     * @return void
     */
    public function test_if_can_notify_a_transaction_for_given_user(): void
    {
        $user = Mockery::mock(User::class);
        $transaction = Mockery::mock(Transaction::class);

        $user->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $transaction->shouldReceive('getAttribute')->with('id')->andReturn(100);

        $user->shouldReceive('notify')
            ->once()
            ->with(Mockery::on(function (DatabaseNotification $notification) use ($user, $transaction) {
                /** @var \App\Models\User $user */
                /** @var \App\Models\Transaction $transaction */
                return $notification->data['userId'] === (string)$user->id &&
                    $notification->data['actionUrl'] === "/profile/?section=transactions&id={$transaction->id}" &&
                    $notification->data['title'] === 'You have a new transaction.';
            }));

        /** @var \App\Models\User $user */
        /** @var \App\Models\Transaction $transaction */
        $this->transactionNotificationService->notifyNewTransaction($user, $transaction);

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Tear down application tests.
     *
     * @return void
     */
    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
