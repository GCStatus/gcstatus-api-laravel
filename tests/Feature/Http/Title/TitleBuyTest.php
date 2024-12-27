<?php

namespace Tests\Feature\Http\Title;

use App\Models\{Status, User, Title, TransactionType};
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyTitle,
    HasDummyUserTitle,
};

class TitleBuyTest extends BaseIntegrationTesting
{
    use HasDummyTitle;
    use HasDummyUserTitle;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The purchasable title.
     *
     * @var \App\Models\Title
     */
    private Title $title;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->actingAsDummyUser();
        $this->title = $this->createDummyTitle([
            'purchasable' => true,
        ]);
    }

    /**
     * Test if can't buy a title if not authenticated.
     *
     * @return void
     */
    public function test_if_cant_buy_a_title_if_not_authenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->postJson(route('titles.buy', $this->title))
            ->assertUnauthorized()
            ->assertSee('We could not authenticate your user. Please, try to login again.');
    }

    /**
     * Test if can throw not found if title doesn't exist.
     *
     * @return void
     */
    public function test_if_can_throw_not_found_if_title_doesnt_exist(): void
    {
        $this->postJson(route('titles.buy', 999999))
            ->assertNotFound()
            ->assertSee('No query results for model [App\\\\Models\\\\Title] 999999');
    }

    /**
     * Test if can't buy a title if title is not available.
     *
     * @return void
     */
    public function test_if_cant_buy_a_title_if_title_is_not_available(): void
    {
        $this->title->update([
            'status_id' => Status::UNAVAILABLE_STATUS_ID,
        ]);

        $this->postJson(route('titles.buy', $this->title))
            ->assertBadRequest()
            ->assertSee('The given title is unavailable!');
    }

    /**
     * Test if can't buy a title if title is not marked as purchasable.
     *
     * @return void
     */
    public function test_if_cant_buy_a_title_if_title_is_not_marked_as_purchasable(): void
    {
        $this->title->update([
            'purchasable' => false,
        ]);

        $this->postJson(route('titles.buy', $this->title))
            ->assertBadRequest()
            ->assertSee('The given title is not purchasable!');
    }

    /**
     * Test if can't buy a title if title cost is invalid.
     *
     * @return void
     */
    public function test_if_cant_buy_a_title_if_title_cost_is_invalid(): void
    {
        $this->title->update([
            'cost' => fake()->numberBetween(-50, 0),
        ]);

        $this->postJson(route('titles.buy', $this->title))
            ->assertBadRequest()
            ->assertSee('The given title is not purchasable!');
    }

    /**
     * Test if can't buy a title if hasn't balance enough to.
     *
     * @return void
     */
    public function test_if_cant_buy_a_title_if_hasnt_balance_enough_to(): void
    {
        $this->postJson(route('titles.buy', $this->title))
            ->assertBadRequest()
            ->assertSee('Your wallet has no balance enough for this operation!');
    }

    /**
     * Test if can't buy a title if user already has given title.
     *
     * @return void
     */
    public function test_if_cant_buy_a_title_if_user_already_has_given_title(): void
    {
        /** @var \App\Models\Wallet $wallet */
        $wallet = $this->user->wallet;

        $wallet->update([
            'balance' => $this->title->cost,
        ]);

        $this->createDummyUserTitleTo($this->user, $this->title);

        $this->postJson(route('titles.buy', $this->title))
            ->assertConflict()
            ->assertSee('The user already has the given title.');

        $wallet->refresh();

        $this->assertEquals($wallet->balance, $this->user->wallet?->balance);
    }

    /**
     * Test if can buy a title.
     *
     * @return void
     */
    public function test_if_can_buy_a_title(): void
    {
        /** @var \App\Models\Wallet $wallet */
        $wallet = $this->user->wallet;

        $wallet->update([
            'balance' => $this->title->cost,
        ]);

        $this->postJson(route('titles.buy', $this->title))->assertOk();
    }

    /**
     * Test if can create a notification for title purchase.
     *
     * @return void
     */
    public function test_if_can_create_a_notification_for_title_purchase(): void
    {
        $this->assertDatabaseEmpty('notifications');

        $this->test_if_can_buy_a_title();

        $notification = [
            'icon' => 'FaMedal',
            'title' => 'You earned a new title!',
            'actionUrl' => "/profile/?section=titles&id={$this->title->id}",
        ];

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->user->id,
            'data' => json_encode($notification),
            'notifiable_type' => $this->user::class,
        ]);
    }

    /**
     * Test if can create a transaction for title purchase operation.
     *
     * @return void
     */
    public function test_if_can_create_a_transaction_for_title_purchase_operation(): void
    {
        $this->assertDatabaseEmpty('transactions');

        $this->test_if_can_buy_a_title();

        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->user->id,
            'amount' => $this->title->cost,
            'transaction_type_id' => TransactionType::SUBTRACTION_TYPE_ID,
            'description' => "You bought the title {$this->title->title} for {$this->title->cost} coins!",
        ]);
    }

    /**
     * Test if can create a notification for the new title purchase transaction.
     *
     * @return void
     */
    public function test_if_can_create_a_notification_for_the_new_title_purchase_transaction(): void
    {
        $this->assertDatabaseEmpty('notifications');

        $this->test_if_can_buy_a_title();

        /** @var \App\Models\Transaction $transaction */
        $transaction = $this->user->transactions->first();

        $notification = [
            'icon' => 'FaDollarSign',
            'title' => 'You have a new transaction.',
            'actionUrl' => "/profile/?section=transactions&id={$transaction->id}",
        ];

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->user->id,
            'data' => json_encode($notification),
            'notifiable_type' => $this->user::class,
        ]);
    }

    /**
     * Test if can deduct correctly user wallet coins amount.
     *
     * @return void
     */
    public function test_if_can_deduct_correctly_user_wallet_coins_amount(): void
    {
        $this->title->update([
            'cost' => 1000,
        ]);

        /** @var \App\Models\Wallet $wallet */
        $wallet = $this->user->wallet;

        $wallet->update([
            'balance' => 2493,
        ]);

        $this->postJson(route('titles.buy', $this->title))->assertOk();

        $this->assertDatabaseHas('wallets', [
            'user_id' => $this->user->id,
            'balance' => $wallet->balance - $this->title->cost,
        ]);
    }

    /**
     * Test if can save the correct data on database.
     *
     * @return void
     */
    public function test_if_can_save_the_correct_data_on_database(): void
    {
        $this->test_if_can_buy_a_title();

        $this->assertDatabaseHas('user_titles', [
            'user_id' => $this->user->id,
            'title_id' => $this->title->id,
        ]);
    }

    /**
     * Test if can respond with valid json structure.
     *
     * @return void
     */
    public function test_if_can_respond_with_valid_json_structure(): void
    {
        /** @var \App\Models\Wallet $wallet */
        $wallet = $this->user->wallet;

        $wallet->update([
            'balance' => $this->title->cost,
        ]);

        $this->postJson(route('titles.buy', $this->title))->assertOk()->assertJsonStructure([
            'data' => [
                'id',
                'cost',
                'own',
                'purchasable',
                'description',
                'created_at',
                'updated_at',
                'status' => [
                    'id',
                    'name',
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
        /** @var \App\Models\Wallet $wallet */
        $wallet = $this->user->wallet;

        $wallet->update([
            'balance' => $this->title->cost,
        ]);

        /** @var \App\Models\Status $status */
        $status = $this->title->status;

        $this->postJson(route('titles.buy', $this->title))->assertOk()->assertJson([
            'data' => [
                'id' => $this->title->id,
                'cost' => $this->title->cost,
                'own' => true,
                'purchasable' => $this->title->purchasable,
                'description' => $this->title->description,
                'created_at' => $this->title->created_at?->toISOString(),
                'updated_at' => $this->title->updated_at?->toISOString(),
                'status' => [
                    'id' => $status->id,
                    'name' => $status->name,
                ],
            ],
        ]);
    }
}
