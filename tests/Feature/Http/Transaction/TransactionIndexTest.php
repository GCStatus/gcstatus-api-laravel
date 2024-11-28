<?php

namespace Tests\Feature\Http\Transaction;

use App\Models\{User, Transaction};
use Tests\Traits\HasDummyTransaction;
use Illuminate\Database\Eloquent\Collection;
use Tests\Feature\Http\BaseIntegrationTesting;

class TransactionIndexTest extends BaseIntegrationTesting
{
    use HasDummyTransaction;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->actingAsDummyUser();
    }

    /**
     * Test if can't get transactions if not authenticated.
     *
     * @return void
     */
    public function test_if_cant_get_transactions_if_not_authenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->getJson(route('transactions.index'))
            ->assertUnauthorized()
            ->assertSee('We could not authenticate your user. Please, try to login again.');
    }

    /**
     * Test if can get transactions if authenticated.
     *
     * @return void
     */
    public function test_if_get_transactions_if_authenticated(): void
    {
        $this->getJson(route('transactions.index'))->assertOk();
    }

    /**
     * Test if can get correct transactions count.
     *
     * @return void
     */
    public function test_if_can_get_correct_transactions_count(): void
    {
        $this->getJson(route('transactions.index'))->assertOk()->assertJsonCount(0, 'data');

        $this->createDummyTransactionToUser($this->user);

        $this->getJson(route('transactions.index'))->assertOk()->assertJsonCount(1, 'data');
    }

    /**
     * Test if another user transactions don't appear on own transactions.
     *
     * @return void
     */
    public function test_if_another_user_transactions_dont_appear_on_own_transactions(): void
    {
        $this->getJson(route('transactions.index'))->assertOk()->assertJsonCount(0, 'data');

        $this->createDummyTransaction();

        $this->getJson(route('transactions.index'))->assertOk()->assertJsonCount(0, 'data');
    }

    /**
     * Test if can respond with valid json structure.
     *
     * @return void
     */
    public function test_if_can_respond_with_valid_json_structure(): void
    {
        $this->createDummyTransactionToUser($this->user);

        $this->getJson(route('transactions.index'))->assertOk()->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'amount',
                    'description',
                    'created_at',
                    'type' => [
                        'id',
                        'type',
                    ],
                ],
            ],
        ]);
    }

    /**
     * Test if can respond with valid json data.
     *
     * @return void
     */
    public function test_if_can_respond_with_valid_json_data(): void
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Transaction> */
        $transactions = Collection::make([
            $this->createDummyTransactionToUser($this->user),
            $this->createDummyTransactionToUser($this->user),
            $this->createDummyTransactionToUser($this->user),
        ]);

        $this->getJson(route('transactions.index'))->assertOk()->assertJson([
            'data' => $transactions->map(function (Transaction $transaction) {
                /** @var \App\Models\TransactionType $transactionType */
                $transactionType = $transaction->type;

                return [
                    'id' => $transaction->id,
                    'amount' => $transaction->amount,
                    'description' => $transaction->description,
                    'created_at' => $transaction->created_at?->toISOString(),
                    'type' => [
                        'id' => $transactionType->id,
                        'type' => $transactionType->type,
                    ],
                ];
            })->toArray(),
        ]);
    }
}
