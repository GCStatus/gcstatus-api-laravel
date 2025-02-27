<?php

namespace Tests\Feature\Http\Admin\Languageable;

use App\Models\{User, Languageable};
use Tests\Traits\HasDummyLanguageable;
use Tests\Feature\Http\BaseIntegrationTesting;

class LanguageableDestroyTest extends BaseIntegrationTesting
{
    use HasDummyLanguageable;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The dummy languageable.
     *
     * @var \App\Models\Languageable
     */
    private Languageable $languageable;

    /**
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'delete:languageables',
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

        $this->languageable = $this->createDummyLanguageable([
            'languageable_id' => 1,
            'languageable_type' => fake()->randomElement(Languageable::ALLOWED_LANGUAGEABLE_TYPES),
        ]);
    }

    /**
     * Test if can't see if is unauthenticated.
     *
     * @return void
     */
    public function test_if_cant_see_if_user_is_unauthenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->deleteJson(route('languageables.destroy', $this->languageable))
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

        $this->deleteJson(route('languageables.destroy', $this->languageable))->assertNotFound();
    }

    /**
     * Test if can soft delete a language.
     *
     * @return void
     */
    public function test_if_can_soft_delete_a_languageable(): void
    {
        $this->assertDatabaseHas('languageables', [
            'id' => $this->languageable->id,
        ]);

        $this->deleteJson(route('languageables.destroy', $this->languageable))->assertOk();

        $this->assertDatabaseMissing('languageables', [
            'id' => $this->languageable->id,
        ]);
    }
}
