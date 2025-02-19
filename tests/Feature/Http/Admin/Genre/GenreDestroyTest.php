<?php

namespace Tests\Feature\Http\Admin\Genre;

use App\Models\{Genre, User};
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyGenre,
    HasDummyPermission,
};

class GenreDestroyTest extends BaseIntegrationTesting
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
     * The dummy genre.
     *
     * @var \App\Models\Genre
     */
    private Genre $genre;

    /**
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'view:genres',
        'delete:genres',
    ];

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->genre = $this->createDummyGenre();

        $this->user = $this->actingAsDummyUser();

        $this->bootUserPermissions($this->user);
    }

    /**
     * Test if can't see if is unauthenticated.
     *
     * @return void
     */
    public function test_if_cant_see_if_user_is_unauthenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->deleteJson(route('genres.destroy', $this->genre))
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

        $this->deleteJson(route('genres.destroy', $this->genre))->assertNotFound();
    }

    /**
     * Test if can soft delete a genre.
     *
     * @return void
     */
    public function test_if_can_soft_delete_a_genre(): void
    {
        $this->assertNotSoftDeleted($this->genre);

        $this->deleteJson(route('genres.destroy', $this->genre))->assertOk();

        $this->assertSoftDeleted($this->genre);
    }
}
