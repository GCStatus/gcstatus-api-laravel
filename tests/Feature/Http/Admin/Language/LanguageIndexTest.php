<?php

namespace Tests\Feature\Http\Admin\Language;

use App\Models\{Language, User};
use Illuminate\Database\Eloquent\Collection;
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyLanguage,
    HasDummyPermission,
};

class LanguageIndexTest extends BaseIntegrationTesting
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
     * The dummy languages.
     *
     * @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Language>
     */
    private Collection $languages;

    /**
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'view:languages',
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

        $this->languages = $this->createDummyLanguages(4);
    }

    /**
     * Test if can't see if is unauthenticated.
     *
     * @return void
     */
    public function test_if_cant_see_if_user_is_unauthenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->getJson(route('languages.index'))
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

        $this->getJson(route('languages.index'))->assertNotFound();
    }

    /**
     * Test if can see Languages if has permissions.
     *
     * @return void
     */
    public function test_if_can_see_Languages_if_has_permissions(): void
    {
        $this->getJson(route('languages.index'))->assertOk();
    }

    /**
     * Test if can get correct json attributes count.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_attributes_count(): void
    {
        $this->getJson(route('languages.index'))->assertOk()->assertJsonCount(4, 'data');
    }

    /**
     * Test if can get correct json structure.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_structure(): void
    {
        $this->getJson(route('languages.index'))->assertOk()->assertJsonStructure([
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
        $this->getJson(route('languages.index'))->assertOk()->assertJson([
            'data' => $this->languages->map(function (Language $language) {
                return [
                    'id' => $language->id,
                    'name' => $language->name,
                    'created_at' => $language->created_at?->toISOString(),
                    'updated_at' => $language->updated_at?->toISOString(),
                ];
            })->toArray(),
        ]);
    }
}
