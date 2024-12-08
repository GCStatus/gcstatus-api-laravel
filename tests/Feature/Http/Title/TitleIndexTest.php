<?php

namespace Tests\Feature\Http\Title;

use App\Models\{User, Status};
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{HasDummyTitle, HasDummyStatus};

class TitleIndexTest extends BaseIntegrationTesting
{
    use HasDummyTitle;
    use HasDummyStatus;

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
     * Test if can't get titles if not authenticated.
     *
     * @return void
     */
    public function test_if_cant_get_titles_if_not_authenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->getJson(route('titles.index'))
            ->assertUnauthorized()
            ->assertSee('We could not authenticate your user. Please, try to login again.');
    }

    /**
     * Test if can get titles if authenticated.
     *
     * @return void
     */
    public function test_if_can_get_titles_if_authenticated(): void
    {
        $this->getJson(route('titles.index'))->assertOk();
    }

    /**
     * Test if can get correct json data count.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_data_count(): void
    {
        $this->getJson(route('titles.index'))->assertOk()->assertJsonCount(0, 'data');

        $this->createDummyTitles(4, [
            'status_id' => Status::AVAILABLE_STATUS_ID,
        ]);

        $this->getJson(route('titles.index'))->assertOk()->assertJsonCount(4, 'data');

        $this->createDummyTitles(4, [
            'status_id' => Status::UNAVAILABLE_STATUS_ID,
        ]);

        $this->getJson(route('titles.index'))->assertOk()->assertJsonCount(8, 'data');
    }

    /**
     * Test if can see if the own changes if user has title.
     *
     * @return void
     */
    public function test_if_can_see_if_the_own_changes_if_user_has_title(): void
    {
        $title = $this->createDummyTitle();

        $this->getJson(route('titles.index'))->assertOk()->assertJson([
            'data' => [
                [
                    'id' => $title->id,
                    'own' => false,
                ],
            ],
        ]);

        $title->users()->save($this->user)->save();

        $this->getJson(route('titles.index'))->assertOk()->assertJson([
            'data' => [
                [
                    'id' => $title->id,
                    'own' => true,
                ],
            ],
        ]);
    }

    /**
     * Test if can get correct json structure.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_structure(): void
    {
        $this->createDummyTitleToUser($this->user);

        $this->getJson(route('titles.index'))->assertOk()->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'own',
                    'cost',
                    'created_at',
                    'updated_at',
                    'purchasable',
                    'description',
                    'status' => [
                        'id',
                        'name',
                    ],
                ],
            ],
        ]);
    }

    /**
     * Test if can get correct json data.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_data(): void
    {
        $title = $this->createDummyTitleToUser($this->user);

        /** @var \App\Models\Status $status */
        $status = $title->status;

        $anotherTitle = $this->createDummyTitle();

        /** @var \App\Models\Status $anotherStatus */
        $anotherStatus = $anotherTitle->status;

        $this->getJson(route('titles.index'))->assertOk()->assertJson([
            'data' => [
                [
                    'id' => $title->id,
                    'own' => true,
                    'cost' => $title->cost,
                    'created_at' => $title->created_at?->toISOString(),
                    'updated_at' => $title->updated_at?->toISOString(),
                    'purchasable' => $title->purchasable,
                    'description' => $title->description,
                    'status' => [
                        'id' => $status->id,
                        'name' => $status->name,
                    ],
                ],
                [
                    'id' => $anotherTitle->id,
                    'own' => false,
                    'cost' => $anotherTitle->cost,
                    'created_at' => $anotherTitle->created_at?->toISOString(),
                    'updated_at' => $anotherTitle->updated_at?->toISOString(),
                    'purchasable' => $anotherTitle->purchasable,
                    'description' => $anotherTitle->description,
                    'status' => [
                        'id' => $anotherStatus->id,
                        'name' => $anotherStatus->name,
                    ],
                ],
            ],
        ]);
    }
}
