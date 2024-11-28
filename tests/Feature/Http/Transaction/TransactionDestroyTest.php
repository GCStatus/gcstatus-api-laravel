<?php

namespace Tests\Feature\Http\Transaction;

use App\Models\{Transaction, User};
use Tests\Traits\HasDummyTransaction;
use Tests\Feature\Http\BaseIntegrationTesting;

class TransactionDestroyTest extends BaseIntegrationTesting
{
    use HasDummyTransaction;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The dummy transaction.
     *
     * @var \App\Models\Transaction
     */
    private Transaction $transaction;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->actingAsDummyUser();
        $this->transaction = $this->createDummyTransactionToUser($this->user);
    }

    /**
     * Test if can't get transactions if not authenticated.
     *
     * @return void
     */
    public function test_if_cant_get_transactions_if_not_authenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->deleteJson(route('transactions.destroy', $this->transaction))
            ->assertUnauthorized()
            ->assertSee('We could not authenticate your user. Please, try to login again.');
    }

    /**
     * Test if can't soft delete a transaction that doesn't belongs to user.
     *
     * @return void
     */
    public function test_if_cant_soft_delete_a_transaction_that_doesnt_belongs_to_user(): void
    {
        $this->deleteJson(route('transactions.destroy', $this->createDummyTransaction()))
            ->assertForbidden()
            ->assertSee('The resource that you are requesting do not belongs to your user.');
    }

    /**
     * Test if can soft delete an own transaction.
     *
     * @return void
     */
    public function test_if_can_soft_delete_an_own_transaction(): void
    {
        $this->deleteJson(route('transactions.destroy', $this->transaction))->assertOk();
    }

    /**
     * Test if can mark transaction as soft delete a transaction.
     *
     * @return void
     */
    public function test_if_can_mark_transaction_as_soft_delete_a_transaction(): void
    {
        $this->assertNotSoftDeleted($this->transaction);

        $this->deleteJson(route('transactions.destroy', $this->transaction))->assertOk();

        $this->assertSoftDeleted($this->transaction);
    }

    /**
     * Test if can respond with correct json structure.
     *
     * @return void
     */
    public function test_if_can_respond_with_correct_json_structure(): void
    {
        $this->deleteJson(route('transactions.destroy', $this->transaction))->assertOk()->assertJsonStructure([
            'data' => [
                'message',
            ],
        ]);
    }

    /**
     * Test if can respond with correct json data.
     *
     * @return void
     */
    public function test_if_can_respond_with_correct_json_data(): void
    {
        $this->deleteJson(route('transactions.destroy', $this->transaction))->assertOk()->assertJson([
            'data' => [
                'message' => 'The transaction was successfully removed!',
            ],
        ]);
    }
}
