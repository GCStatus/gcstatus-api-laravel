<?php

namespace Tests\Feature\Http\Level;

use App\Models\{Level, User, TransactionType};
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyTitle,
    HasDummyRewardable,
    HasDummyTransaction,
    HasDummyUserMission,
};

class LevelUpTest extends BaseIntegrationTesting
{
    use HasDummyTitle;
    use HasDummyRewardable;
    use HasDummyTransaction;
    use HasDummyUserMission;

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
     * Test if can create a transaction for earning coin from leveling up.
     *
     * @return void
     */
    public function test_if_can_create_a_transaction_for_earning_coin_for_leveling_up(): void
    {
        $this->assertDatabaseEmpty('transactions');

        awarder()->awardExperience($this->user, 100); // enough experience for level 2.

        /** @var \App\Models\Level $level */
        $level = Level::where('level', 2)->firstOrFail();

        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->user->id,
            'amount' => $level->coins,
            'transaction_type_id' => TransactionType::ADDITION_TYPE_ID,
            'description' => "You earned {$level->coins} for leveling up!",
        ]);
    }

    /**
     * Test if can create a notification for the created transaction of leveling up.
     *
     * @return void
     */
    public function test_if_can_create_a_notification_for_the_created_transaction_of_leveling_up(): void
    {
        $this->assertDatabaseEmpty('notifications');

        awarder()->awardExperience($this->user, 100); // enough experience for level 2.

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
     * Test if can create a notification for leveling up.
     *
     * @return void
     */
    public function test_if_can_create_a_notification_for_leveling_up(): void
    {
        $this->assertDatabaseEmpty('notifications');

        awarder()->awardExperience($this->user, 100); // enough experience for level 2.

        /** @var \App\Models\Level $level */
        $level = Level::where('level', 2)->firstOrFail();

        $notification = [
            'icon' => 'FaLevelUpAlt',
            'title' => "Congratulations for reaching a new level! You are now on Level {$level->level}.",
            'actionUrl' => '/profile/?section=levels',
        ];

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->user->id,
            'data' => json_encode($notification),
            'notifiable_type' => $this->user::class,
        ]);
    }

    /**
     * Test if can create a notification for earning experience.
     *
     * @return void
     */
    public function test_if_can_create_a_notification_for_earning_experience(): void
    {
        $this->assertDatabaseEmpty('notifications');

        awarder()->awardExperience($this->user, $amount = 100); // enough experience for level 2.

        $notification = [
            'icon' => 'FaAnglesUp',
            'title' => "You received $amount experience.",
            'actionUrl' => '/profile',
        ];

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->user->id,
            'data' => json_encode($notification),
            'notifiable_type' => $this->user::class,
        ]);
    }

    /**
     * Test if can deduct experience correctly from user on level up.
     *
     * @return void
     */
    public function test_if_can_deduct_experience_correctly_from_user_on_level_up(): void
    {
        awarder()->awardExperience($this->user, 110); // enough for level 2, remains 10.

        $this->user->refresh();

        $this->assertEquals(2, $this->user->level_id);
        $this->assertEquals(10, $this->user->experience);

        awarder()->awardExperience($this->user, 110); // not enough for level 3, 120 in total.

        $this->user->refresh();

        $this->assertEquals(2, $this->user->level_id);
        $this->assertEquals(120, $this->user->experience);

        awarder()->awardExperience($this->user, 100); // enough for level 3, remains 20.

        $this->user->refresh();

        $this->assertEquals(3, $this->user->level_id);
        $this->assertEquals(20, $this->user->experience);
    }

    /**
     * Test if can get correctly coins amount on leveling up.
     *
     * @return void
     */
    public function test_if_can_get_correctly_coins_amount_on_leveling_up(): void
    {
        /** @var \App\Models\Level $level2Amount */
        $level2Amount = Level::where('level', 2)->firstOrFail();

        $this->assertDatabaseHas('wallets', [
            'balance' => 0,
            'user_id' => $this->user->id,
        ]);

        awarder()->awardExperience($this->user, 100); // enough for level 2.

        $amount = $level2Amount->coins;

        $this->assertDatabaseHas('wallets', [
            'balance' => $amount,
            'user_id' => $this->user->id,
        ]);

        $this->user->refresh();

        awarder()->awardExperience($this->user, 1700); // enough for level 5.

        $amount = Level::all()->sum(fn (Level $level) => $level->coins);

        $this->assertDatabaseHas('wallets', [
            'balance' => $amount,
            'user_id' => $this->user->id,
        ]);
    }

    /**
     * Test if can award user with title if applicable.
     *
     * @return void
     */
    public function test_if_can_award_user_with_title_if_applicable(): void
    {
        $this->assertDatabaseEmpty('user_titles');

        /** @var \App\Models\Level $level2 */
        $level2 = Level::where('level', 2)->firstOrFail();

        $title = $this->createDummyTitle();

        $this->createDummyRewardable([
            'sourceable_id' => $level2->id,
            'sourceable_type' => $level2::class,
            'rewardable_id' => $title->id,
            'rewardable_type' => $title::class,
        ]);

        awarder()->awardExperience($this->user, 100); // enough for level 2.

        $this->assertDatabaseHas('user_titles', [
            'title_id' => $title->id,
            'user_id' => $this->user->id,
        ]);
    }

    /**
     * Test if can notify the user from new title of leveling up.
     *
     * @return void
     */
    public function test_if_can_notify_the_user_from_new_title_of_leveling_up(): void
    {
        $this->assertDatabaseEmpty('notifications');

        /** @var \App\Models\Level $level2 */
        $level2 = Level::where('level', 2)->firstOrFail();

        $title = $this->createDummyTitle();

        $this->createDummyRewardable([
            'sourceable_id' => $level2->id,
            'sourceable_type' => $level2::class,
            'rewardable_id' => $title->id,
            'rewardable_type' => $title::class,
        ]);

        awarder()->awardExperience($this->user, 100); // enough for level 2.

        $notification = [
            'icon' => 'FaMedal',
            'title' => 'You earned a new title!',
            'actionUrl' => "/profile/?section=titles&id={$title->id}",
        ];

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->user->id,
            'data' => json_encode($notification),
            'notifiable_type' => $this->user::class,
        ]);
    }
}
