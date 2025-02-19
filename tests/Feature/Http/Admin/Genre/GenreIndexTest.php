<?php

namespace Tests\Feature\Http\Admin\Tag;

use App\Models\{Genre, User};
use Illuminate\Database\Eloquent\Collection;
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyGenre,
    HasDummyPermission,
};

class GenreIndexTest extends BaseIntegrationTesting
{
    use HasDummyGenre;
    use HasDummyPermission;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The dummy genres.
     *
     * @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Genre>
     */
    private Collection $genres;

    /**
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'view:genres',
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

        $this->genres = $this->createDummyGenres(4);
    }

    /**
     * Test if can't see if is unauthenticated.
     *
     * @return void
     */
    public function test_if_cant_see_if_user_is_unauthenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->getJson(route('genres.index'))
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

        $this->getJson(route('genres.index'))->assertNotFound();
    }

    /**
     * Test if can see genres if has permissions.
     *
     * @return void
     */
    public function test_if_can_see_genres_if_has_permissions(): void
    {
        $this->getJson(route('genres.index'))->assertOk();
    }

    /**
     * Test if can get correct json attributes count.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_attributes_count(): void
    {
        $this->getJson(route('genres.index'))->assertOk()->assertJsonCount(4, 'data');
    }

    /**
     * Test if can get correct json structure.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_structure(): void
    {
        $this->getJson(route('genres.index'))->assertOk()->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'created_at',
                    'updated_at',
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
        $this->getJson(route('genres.index'))->assertOk()->assertJson([
            'data' => $this->genres->map(function (Genre $genre) {
                return [
                    'id' => $genre->id,
                    'name' => $genre->name,
                    'created_at' => $genre->created_at?->toISOString(),
                    'updated_at' => $genre->updated_at?->toISOString(),
                ];
            })->toArray(),
        ]);
    }
}
