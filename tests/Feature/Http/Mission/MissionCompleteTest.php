<?php

namespace Tests\Feature\Http\Mission;

use Illuminate\Support\Facades\Bus;
use Tests\Feature\Http\BaseIntegrationTesting;
use App\Jobs\{
    CompleteMissionJob,
    GiveMissionRewardsJob,
};
use App\Models\{
    User,
    Level,
    Status,
    Mission,
    TransactionType,
    MissionRequirement,
};
use Tests\Traits\{
    HasDummyTitle,
    HasDummyMission,
    HasDummyRewardable,
    HasDummyTransaction,
    HasDummyUserMission,
    HasDummyMissionRequirement,
};

class MissionCompleteTest extends BaseIntegrationTesting
{
    use HasDummyTitle;
    use HasDummyMission;
    use HasDummyRewardable;
    use HasDummyTransaction;
    use HasDummyUserMission;
    use HasDummyMissionRequirement;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The dummy mission.
     *
     * @var \App\Models\Mission
     */
    private Mission $mission;

    /**
     * The dummy mission requirement.
     *
     * @var \App\Models\MissionRequirement
     */
    private MissionRequirement $missionRequirement;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->actingAsDummyUser();
        $this->mission = $this->createDummyMission([
            'for_all' => true,
        ]);

        $this->missionRequirement = $this->createDummyMissionRequirementTo($this->mission, [
            'goal' => fake()->numberBetween(1, 5),
            'key' => MissionRequirement::TRANSACTIONS_COUNT_STRATEGY_KEY,
        ]);
    }

    /**
     * Test if can't complete a mission if not authenticated.
     *
     * @return void
     */
    public function test_if_cant_complete_a_mission_if_not_authenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->postJson(route('missions.complete', $this->mission))
            ->assertUnauthorized()
            ->assertSee('We could not authenticate your user. Please, try to login again.');
    }

    /**
     * Test if can throw not found if mission didn't exists.
     *
     * @return void
     */
    public function test_if_can_throw_not_found_if_mission_didnt_exists(): void
    {
        $this->postJson(route('missions.complete', 9999999))
            ->assertNotFound()
            ->assertSee('No query results for model [App\\\\Models\\\\Mission] 9999999');
    }

    /**
     * Test if can't complete unavailable missions.
     *
     * @return void
     */
    public function test_if_cant_complete_unavailable_missions(): void
    {
        $this->mission->update([
            'status_id' => Status::UNAVAILABLE_STATUS_ID,
        ]);

        $this->postJson(route('missions.complete', $this->mission))
            ->assertBadRequest()
            ->assertSee('The given mission is not available.');
    }

    /**
     * Test if can't complete mission if mission is not for all and user is not target.
     *
     * @return void
     */
    public function test_if_cant_complete_mission_if_mission_is_not_for_all_and_user_is_not_target(): void
    {
        $this->mission->update([
            'for_all' => false,
        ]);

        $this->postJson(route('missions.complete', $this->mission))
            ->assertForbidden()
            ->assertSee('Ops! Something wrong happened: you can not complete the given mission.');
    }

    /**
     * Test if can't complete mission if user hasn't complete progress for it requirements.
     *
     * @return void
     */
    public function test_if_cant_complete_mission_if_user_hasnt_complete_progress_for_it_requirements(): void
    {
        $this->postJson(route('missions.complete', $this->mission))
            ->assertBadRequest()
            ->assertSee('You did not complete this mission yet. Please, double check it and try again later!');
    }

    /**
     * Test if can complete mission if requirements met.
     *
     * @return void
     */
    public function test_if_can_complete_mission_if_requirements_met(): void
    {
        $this->createDummyTransactions($this->missionRequirement->goal, [
            'user_id' => $this->user->id,
        ]);

        $this->postJson(route('missions.complete', $this->mission))->assertOk();
    }

    /**
     * Test if can dispatch the complete mission job.
     *
     * @return void
     */
    public function test_if_can_dispatch_the_complete_mission_job(): void
    {
        Bus::fake();

        $this->createDummyTransactions($this->missionRequirement->goal, [
            'user_id' => $this->user->id,
        ]);

        $this->postJson(route('missions.complete', $this->mission))->assertOk();

        Bus::assertDispatched(CompleteMissionJob::class, function (CompleteMissionJob $job) {
            return $job->user->id === $this->user->id && $job->mission->id === $this->mission->id;
        });
    }

    /**
     * Test if can update or create the user mission proress if mission is completed.
     *
     * @return void
     */
    public function test_if_can_update_or_create_the_user_mission_progress_if_mission_is_completed(): void
    {
        $this->createDummyTransactions($this->missionRequirement->goal, [
            'user_id' => $this->user->id,
        ]);

        $this->postJson(route('missions.complete', $this->mission))->assertOk();

        $this->assertDatabaseHas('user_mission_progress', [
            'completed' => true,
            'user_id' => $this->user->id,
            'progress' => $this->missionRequirement->goal,
            'mission_requirement_id' => $this->missionRequirement->id,
        ]);
    }

    /**
     * Test if can dispatch the chained job to give mission rewards.
     *
     * @return void
     */
    public function test_if_can_dispatch_the_chained_job_to_give_mission_rewards(): void
    {
        Bus::fake();

        $this->createDummyTransactions($this->missionRequirement->goal, [
            'user_id' => $this->user->id,
        ]);

        $this->postJson(route('missions.complete', $this->mission))->assertOk();

        Bus::assertDispatched(CompleteMissionJob::class, function (CompleteMissionJob $job) {
            $job->handle();

            return $job->user->id === $this->user->id && $job->mission->id === $this->mission->id;
        });

        Bus::assertDispatched(GiveMissionRewardsJob::class, function (GiveMissionRewardsJob $job) {
            return $job->user->id === $this->user->id && $job->mission->id === $this->mission->id;
        });
    }

    /**
     * Test if can't give user rewards if user already completed mission.
     *
     * @return void
     */
    public function test_if_cant_give_user_rewards_if_user_already_completed_mission(): void
    {
        /** @var \App\Models\Wallet $wallet */
        $wallet = $this->user->wallet;

        $this->assertEquals(0, $wallet->balance);
        $this->assertEquals(0, $this->user->experience);

        $this->createDummyUserMission([
            'completed' => true,
            'user_id' => $this->user->id,
            'last_completed_at' => now(),
            'mission_id' => $this->mission->id,
        ]);

        $this->createDummyTransactions($this->missionRequirement->goal, [
            'user_id' => $this->user->id,
        ]);

        $this->postJson(route('missions.complete', $this->mission))->assertOk();

        $wallet->refresh();
        $this->user->refresh();

        $this->assertEquals(0, $wallet->balance);
        $this->assertEquals(0, $this->user->experience);
    }

    /**
     * Test if can award user with mission coins and experience on completion.
     *
     * @return void
     */
    public function test_if_can_award_user_with_mission_coins_and_experience_on_completion(): void
    {
        $this->mission->update([
            'experience' => 1,
        ]);

        /** @var \App\Models\Wallet $wallet */
        $wallet = $this->user->wallet;

        $this->assertEquals(0, $wallet->balance);
        $this->assertEquals(0, $this->user->experience);

        $this->createDummyTransactions($this->missionRequirement->goal, [
            'user_id' => $this->user->id,
        ]);

        $this->postJson(route('missions.complete', $this->mission))->assertOk();

        $wallet->refresh();
        $this->user->refresh();

        $this->assertEquals($this->mission->coins, $wallet->balance);
        $this->assertEquals($this->mission->experience, $this->user->experience);
    }

    /**
     * Test if can award user with mission reward as title.
     *
     * @return void
     */
    public function test_if_can_award_user_with_mission_reward_as_title(): void
    {
        $title = $this->createDummyTitle();

        $this->createDummyRewardable([
            'rewardable_id' => $title->id,
            'rewardable_type' => $title::class,
            'sourceable_id' => $this->mission->id,
            'sourceable_type' => $this->mission::class,
        ]);

        $this->assertDatabaseEmpty('user_titles');

        $this->createDummyTransactions($this->missionRequirement->goal, [
            'user_id' => $this->user->id,
        ]);

        $this->postJson(route('missions.complete', $this->mission))->assertOk();

        $this->assertDatabaseHas('user_titles', [
            'title_id' => $title->id,
            'user_id' => $this->user->id,
        ]);
    }

    /**
     * Test if can mark mission as complete on mission completion.
     *
     * @return void
     */
    public function test_if_can_mark_mission_as_complete_on_mission_completion(): void
    {
        $this->assertDatabaseEmpty('user_missions');

        $this->createDummyTransactions($this->missionRequirement->goal, [
            'user_id' => $this->user->id,
        ]);

        $this->postJson(route('missions.complete', $this->mission))->assertOk();

        $this->assertDatabaseHas('user_missions', [
            'completed' => true,
            'user_id' => $this->user->id,
            'mission_id' => $this->mission->id,
        ]);
    }

    /**
     * Test if can complete a mission if mission is not for all but user is target.
     *
     * @return void
     */
    public function test_if_can_complete_a_mission_if_mission_is_not_for_all_but_user_is_target(): void
    {
        $this->mission->update([
            'for_all' => false,
        ]);

        $this->mission->users()->attach($this->user->id);

        $this->createDummyTransactions($this->missionRequirement->goal, [
            'user_id' => $this->user->id,
        ]);

        $this->postJson(route('missions.complete', $this->mission))->assertOk();
    }

    /**
     * Test if can create a transaction for the coins transfer on mission complete.
     *
     * @return void
     */
    public function test_if_can_create_a_transaction_for_the_coins_transfer_on_mission_complete(): void
    {
        $this->assertDatabaseEmpty('transactions');

        $this->createDummyTransactions($this->missionRequirement->goal, [
            'user_id' => $this->user->id,
        ]);

        $this->postJson(route('missions.complete', $this->mission))->assertOk();

        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->user->id,
            'amount' => $this->mission->coins,
            'transaction_type_id' => TransactionType::ADDITION_TYPE_ID,
            'description' => "You earned {$this->mission->coins} for completing the mission {$this->mission->mission}.",
        ]);
    }

    /**
     * Test if can create a notification for the created transaction.
     *
     * @return void
     */
    public function test_if_can_create_a_notification_for_the_created_transaction_on_mission_complete(): void
    {
        $this->assertDatabaseEmpty('notifications');

        $this->createDummyTransactions($this->missionRequirement->goal, [
            'user_id' => $this->user->id,
        ]);

        $this->postJson(route('missions.complete', $this->mission))->assertOk();

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
     * Test if can level up if mission give enough experience.
     *
     * @return void
     */
    public function test_if_can_level_up_if_mission_give_enough_experience(): void
    {
        $this->mission->update([
            'experience' => 100, // Enough amount to get level 2.
        ]);

        /** @var \App\Models\Level $level */
        $level = $this->user->level;

        $this->assertEquals(1, $level->level);

        $this->createDummyTransactions($this->missionRequirement->goal, [
            'user_id' => $this->user->id,
        ]);

        $this->postJson(route('missions.complete', $this->mission))->assertOk();

        $this->user->refresh();

        /** @var \App\Models\Level $level */
        $level = $this->user->level;

        $this->assertEquals(2, $level->level);
    }

    /**
     * Test if can get mission and level up coins amount.
     *
     * @return void
     */
    public function test_if_can_get_mission_and_level_up_coins_amount(): void
    {
        /** @var \App\Models\Level $level2Amount */
        $level2Amount = Level::where('level', 2)->firstOrFail();

        $this->mission->update([
            'coins' => 10,
            'experience' => 100, // Enough amount to get level 2.
        ]);

        /** @var \App\Models\Wallet $wallet */
        $wallet = $this->user->wallet;

        $this->assertEquals(0, $wallet->balance);

        $this->createDummyTransactions($this->missionRequirement->goal, [
            'user_id' => $this->user->id,
        ]);

        $this->postJson(route('missions.complete', $this->mission))->assertOk();

        $wallet->refresh();
        $this->user->refresh();

        $amount = $this->mission->coins + $level2Amount->coins;

        $this->assertEquals($amount, $wallet->balance);
    }

    /**
     * Test if can notify the experience earning for the user.
     *
     * @return void
     */
    public function test_if_can_notify_the_experience_earning_for_the_user(): void
    {
        $this->mission->update([
            'experience' => 50,
        ]);

        $this->assertDatabaseEmpty('notifications');

        $this->createDummyTransactions($this->missionRequirement->goal, [
            'user_id' => $this->user->id,
        ]);

        $this->postJson(route('missions.complete', $this->mission))->assertOk();

        $notification = [
            'icon' => 'FaAnglesUp',
            'title' => "You received {$this->mission->experience} experience.",
            'actionUrl' => '/profile',
        ];

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->user->id,
            'data' => json_encode($notification),
            'notifiable_type' => $this->user::class,
        ]);
    }
}
