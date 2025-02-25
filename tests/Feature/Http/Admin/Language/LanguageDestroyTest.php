<?php

namespace Tests\Feature\Http\Admin\Language;

use App\Models\{Language, User};
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyLanguage,
    HasDummyPermission,
};

class LanguageDestroyTest extends BaseIntegrationTesting
{
    use HasDummyLanguage;
    use HasDummyPermission;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The dummy language.
     *
     * @var \App\Models\Language
     */
    private Language $language;

    /**
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'view:languages',
        'delete:languages',
    ];

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->language = $this->createDummyLanguage();

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

        $this->deleteJson(route('languages.destroy', $this->language))
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

        $this->deleteJson(route('languages.destroy', $this->language))->assertNotFound();
    }

    /**
     * Test if can soft delete a language.
     *
     * @return void
     */
    public function test_if_can_soft_delete_a_language(): void
    {
        $this->assertNotSoftDeleted($this->language);

        $this->deleteJson(route('languages.destroy', $this->language))->assertOk();

        $this->assertSoftDeleted($this->language);
    }
}
