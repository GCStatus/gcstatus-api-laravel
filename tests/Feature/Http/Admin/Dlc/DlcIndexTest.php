<?php

namespace Tests\Feature\Http\Admin\Dlc;

use App\Models\{Dlc, User};
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyDlc,
    HasDummyPermission,
};

class DlcIndexTest extends BaseIntegrationTesting
{
    use HasDummyDlc;
    use HasDummyPermission;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The dummy dlcs.
     *
     * @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Dlc>
     */
    private Collection $dlcs;

    /**
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'view:dlcs',
    ];

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->actingAsDummyUser();

        $this->bootUserPermissions($this->user);

        $this->dlcs = $this->createDummyDlcs(4);
    }

    /**
     * Test if can't see if is unauthenticated.
     *
     * @return void
     */
    public function test_if_cant_see_if_user_is_unauthenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->getJson(route('dlcs.index'))
            ->assertUnauthorized()
            ->assertSee('We could not authenticate your user. Please, try to login again.');
    }

    /**
     * Test if can't see if hasn't permissions.
     *
     * @return void
     */
    public function test_if_cant_see_if_hasnt_permissions(): void
    {
        $this->user->permissions()->detach();

        $this->getJson(route('dlcs.index'))->assertNotFound();
    }

    /**
     * Test if can see dlcs if has permissions.
     *
     * @return void
     */
    public function test_if_can_see_dlcs_if_has_permissions(): void
    {
        $this->getJson(route('dlcs.index'))->assertOk();
    }

    /**
     * Test if can get correct json attributes count.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_attributes_count(): void
    {
        $this->getJson(route('dlcs.index'))->assertOk()->assertJsonCount(4, 'data');
    }

    /**
     * Test if can get correct json structure.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_structure(): void
    {
        $this->getJson(route('dlcs.index'))->assertOk()->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'slug',
                    'free',
                    'title',
                    'cover',
                    'legal',
                    'about',
                    'description',
                    'release_date',
                    'short_description',
                    'created_at',
                    'updated_at',
                    'game' => [
                        'id',
                        'age',
                        'slug',
                        'free',
                        'title',
                        'cover',
                        'about',
                        'legal',
                        'website',
                        'views_count',
                        'condition',
                        'description',
                        'release_date',
                        'great_release',
                        'short_description',
                        'hearts_count',
                        'created_at',
                        'updated_at',
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
        $this->getJson(route('dlcs.index'))->assertOk()->assertJson([
            'data' => $this->dlcs->map(function (Dlc $dlc) {
                /** @var \App\Models\Game $game */
                $game = $dlc->game;

                return [
                    'id' => $dlc->id,
                    'slug' => $dlc->slug,
                    'free' => $dlc->free,
                    'title' => $dlc->title,
                    'cover' => $dlc->cover,
                    'legal' => $dlc->legal,
                    'about' => $dlc->about,
                    'description' => $dlc->description,
                    'release_date' => Carbon::parse($dlc->release_date)->toISOString(),
                    'short_description' => $dlc->short_description,
                    'created_at' => $dlc->created_at?->toISOString(),
                    'updated_at' => $dlc->updated_at?->toISOString(),
                    'game' => [
                        'id' => $game->id,
                        'age' => $game->age,
                        'slug' => $game->slug,
                        'free' => $game->free,
                        'title' => $game->title,
                        'cover' => $game->cover,
                        'about' => $game->about,
                        'legal' => $game->legal,
                        'website' => $game->website,
                        'views_count' => 0,
                        'condition' => $game->condition,
                        'description' => $game->description,
                        'release_date' => Carbon::parse($game->release_date)->toISOString(),
                        'great_release' => $game->great_release,
                        'short_description' => $game->short_description,
                        'hearts_count' => 0,
                        'comments_count' => 0,
                        'created_at' => $game->created_at?->toISOString(),
                        'updated_at' => $game->updated_at?->toISOString(),
                    ],
                ];
            })->toArray(),
        ]);
    }
}
