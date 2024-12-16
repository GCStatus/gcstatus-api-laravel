<?php

namespace Tests\Feature\Http\Mission;

use App\Models\User;
use Illuminate\Support\Carbon;
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{HasDummyTitle, HasDummyMission, HasDummyMissionRequirement};

class MissionIndexTest extends BaseIntegrationTesting
{
    use HasDummyTitle;
    use HasDummyMission;
    use HasDummyMissionRequirement;

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
     * Test if can't get missions if not authenticated.
     *
     * @return void
     */
    public function test_if_cant_get_missions_if_not_authenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->getJson(route('missions.index'))
            ->assertUnauthorized()
            ->assertSee('We could not authenticate your user. Please, try to login again.');
    }

    /**
     * Test if can get missions.
     *
     * @return void
     */
    public function test_if_can_get_missions(): void
    {
        $this->getJson(route('missions.index'))->assertOk();
    }

    /**
     * Test if can't get missions that user is not target.
     *
     * @return void
     */
    public function test_if_cant_get_missions_that_user_is_not_target(): void
    {
        $this->getJson(route('missions.index'))->assertOk()->assertJsonCount(0, 'data');

        $this->createDummyMission([
            'for_all' => false,
        ]);

        $this->getJson(route('missions.index'))->assertOk()->assertJsonCount(0, 'data');
    }

    /**
     * Test if can get correct missions count.
     *
     * @return void
     */
    public function test_if_can_get_correct_missions_count(): void
    {
        $this->getJson(route('missions.index'))->assertOk()->assertJsonCount(0, 'data');

        $this->createDummyMission([
            'for_all' => true,
        ]);

        $this->getJson(route('missions.index'))->assertOk()->assertJsonCount(1, 'data');

        $this->createDummyMission([
            'for_all' => false,
        ]);

        $this->getJson(route('missions.index'))->assertOk()->assertJsonCount(1, 'data');
    }

    /**
     * Test if can get missions that user is target.
     *
     * @return void
     */
    public function test_if_can_get_missions_that_user_is_target(): void
    {
        $mission = $this->createDummyMission([
            'for_all' => false,
        ]);

        $mission->users()->attach($this->user->id);

        $this->getJson(route('missions.index'))->assertOk()->assertJsonCount(1, 'data');
    }

    /**
     * Test if can get for all missions.
     *
     * @return void
     */
    public function test_if_can_get_for_all_missions(): void
    {
        $this->createDummyMission([
            'for_all' => true,
        ]);

        $this->getJson(route('missions.index'))->assertOk()->assertJsonCount(1, 'data');
    }

    /**
     * Test if can get correct mission json structure.
     *
     * @return void
     */
    public function test_if_can_get_correct_mission_json_structure(): void
    {
        $mission = $this->createDummyMission([
            'for_all' => true,
        ]);

        /** @var \App\Models\Title $title */
        $title = $this->createDummyTitle();

        $mission->rewards()->create([
            'sourceable_id' => $mission->id,
            'sourceable_type' => $mission::class,
            'rewardable_id' => $title->id,
            'rewardable_type' => $title::class,
        ]);

        $mission->userMission()->create([
            'user_id' => $this->user->id,
            'completed' => fake()->boolean(),
            'last_completed_at' => Carbon::now()->subDays(fake()->numberBetween(1, 10)),
        ]);

        $missionRequirement = $this->createDummyMissionRequirementTo($mission);

        $missionRequirement->userProgress()->create([
            'user_id' => $this->user->id,
            'completed' => fake()->boolean(),
            'progress' => fake()->numberBetween(1, 10),
        ]);

        $this->getJson(route('missions.index'))->assertOk()->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'coins',
                    'mission',
                    'for_all',
                    'frequency',
                    'experience',
                    'description',
                    'status' => [
                        'id',
                        'name',
                    ],
                    'progress' => [
                        'id',
                        'completed',
                        'last_completed_at',
                    ],
                    'rewards' => [
                        '*' => [
                            'id',
                            'rewardable_type',
                            'sourceable_type',
                            'rewardable' => [
                                'id',
                                'own',
                                'cost',
                                'purchasable',
                                'description',
                                'created_at',
                                'updated_at',
                                'status' => [
                                    'id',
                                    'name',
                                ],
                            ],
                        ],
                    ],
                    'requirements' => [
                        '*' => [
                            'id',
                            'goal',
                            'task',
                            'description',
                            'created_at',
                            'updated_at',
                            'progress' => [
                                'id',
                                'progress',
                                'completed',
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Test if can get correct missions json data.
     *
     * @return void
     */
    public function test_if_can_get_correct_mission_json_data(): void
    {
        $mission = $this->createDummyMission([
            'for_all' => true,
        ]);

        /** @var \App\Models\Status $missionStatus */
        $missionStatus = $mission->status;

        /** @var \App\Models\Title $title */
        $title = $this->createDummyTitle();

        /** @var \App\Models\Rewardable $rewardable */
        $rewardable = $mission->rewards()->create([
            'sourceable_id' => $mission->id,
            'sourceable_type' => $mission::class,
            'rewardable_id' => $title->id,
            'rewardable_type' => $title::class,
        ]);

        /** @var \App\Models\Title $reward */
        $reward = $rewardable->rewardable;

        /** @var \App\Models\Status $titleStatus */
        $titleStatus = $reward->status;

        /** @var \App\Models\UserMission $userMission */
        $userMission = $mission->userMission()->create([
            'user_id' => $this->user->id,
            'completed' => fake()->boolean(),
            'last_completed_at' => Carbon::now()->subDays(fake()->numberBetween(1, 10)),
        ]);

        $missionRequirement = $this->createDummyMissionRequirementTo($mission);

        /** @var \App\Models\UserMissionProgress $userMissionProgress */
        $userMissionProgress = $missionRequirement->userProgress()->create([
            'user_id' => $this->user->id,
            'completed' => fake()->boolean(),
            'progress' => fake()->numberBetween(1, 10),
        ]);

        $this->getJson(route('missions.index'))->assertOk()->assertJson([
            'data' => [
                [
                    'id' => $mission->id,
                    'coins' => $mission->coins,
                    'mission' => $mission->mission,
                    'for_all' => $mission->for_all,
                    'frequency' => $mission->frequency,
                    'experience' => $mission->experience,
                    'description' => $mission->description,
                    'status' => [
                        'id' => $missionStatus->id,
                        'name' => $missionStatus->name,
                    ],
                    'progress' => [
                        'id' => $userMission->id,
                        'completed' => $userMission->completed,
                        'last_completed_at' => $userMission->last_completed_at,
                    ],
                    'rewards' => [
                        [
                            'id' => $rewardable->id,
                            'rewardable_type' => $rewardable->rewardable_type,
                            'sourceable_type' => $rewardable->sourceable_type,
                            'rewardable' => [
                                'id' => $reward->id,
                                'own' => false,
                                'cost' => $reward->cost,
                                'purchasable' => $reward->purchasable,
                                'description' => $reward->description,
                                'created_at' => $reward->created_at?->toISOString(),
                                'updated_at' => $reward->updated_at?->toISOString(),
                                'status' => [
                                    'id' => $titleStatus->id,
                                    'name' => $titleStatus->name,
                                ],
                            ],
                        ],
                    ],
                    'requirements' => [
                        [
                            'id' => $missionRequirement->id,
                            'goal' => $missionRequirement->goal,
                            'task' => $missionRequirement->task,
                            'description' => $missionRequirement->description,
                            'created_at' => $missionRequirement->created_at?->toISOString(),
                            'updated_at' => $missionRequirement->updated_at?->toISOString(),
                            'progress' => [
                                'id' => $userMissionProgress->id,
                                'progress' => $userMissionProgress->progress,
                                'completed' => $userMissionProgress->completed,
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
