<?php

namespace Tests\Feature\Http\Admin\Languageable;

use Mockery;
use Exception;
use App\Models\{User, Languageable};
use Tests\Traits\HasDummyLanguageable;
use Tests\Feature\Http\BaseIntegrationTesting;
use App\Contracts\Services\{LogServiceInterface, LanguageableServiceInterface};

class LanguageableUpdateTest extends BaseIntegrationTesting
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
        'update:languageables',
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
     * Get valid payload.
     *
     * @return array<string, mixed>
     */
    private function getValidPayload(): array
    {
        return [
            'menu' => fake()->boolean(),
            'dubs' => fake()->boolean(),
            'subtitles' => fake()->boolean(),
        ];
    }

    /**
     * Test if can't see if is unauthenticated.
     *
     * @return void
     */
    public function test_if_cant_see_if_user_is_unauthenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->putJson(route('languageables.update', $this->languageable), $this->getValidPayload())
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

        $this->putJson(route('languageables.update', $this->languageable), $this->getValidPayload())->assertNotFound();
    }

    /**
     * Test if can't create a languageable without payload.
     *
     * @return void
     */
    public function test_if_cant_create_a_languageable_without_payload(): void
    {
        $this->putJson(route('languageables.update', $this->languageable))->assertUnprocessable();
    }

    /**
     * Test if can throw correct invalid json keys.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_keys(): void
    {
        $this->putJson(route('languageables.update', $this->languageable))
            ->assertUnprocessable()
            ->assertInvalid(['menu', 'dubs', 'subtitles']);
    }

    /**
     * Test if can throw correct invalid json messages.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_messages(): void
    {
        $this->putJson(route('languageables.update', $this->languageable))
            ->assertUnprocessable()
            ->assertInvalid(['menu', 'dubs', 'subtitles'])
            ->assertSee('The menu field is required. (and 2 more errors)');
    }

    /**
     * Test if can update languageable with valid payload.
     *
     * @return void
     */
    public function test_if_can_update_languageable_with_valid_payload(): void
    {
        $this->putJson(route('languageables.update', $this->languageable), $this->getValidPayload())->assertOk();
    }

    /**
     * Test if can save new languageable on database.
     *
     * @return void
     */
    public function test_if_can_save_new_languageable_on_database(): void
    {
        $this->putJson(route('languageables.update', $this->languageable), $data = $this->getValidPayload())->assertOk();

        $this->assertDatabaseHas('languageables', [
            'menu' => $data['menu'],
            'dubs' => $data['dubs'],
            'subtitles' => $data['subtitles'],
        ]);
    }

    /**
     * Test if can get correct json structure on response.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_structure_on_response(): void
    {
        $this->putJson(route('languageables.update', $this->languageable), $this->getValidPayload())->assertOk()->assertJsonStructure([
            'data' => [
                'id',
                'menu',
                'dubs',
                'subtitles',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    /**
     * Test if can get correct json data on response.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_data_on_response(): void
    {
        $this->putJson(route('languageables.update', $this->languageable), $data = $this->getValidPayload())->assertOk()->assertJson([
            'data' => [
                'menu' => $data['menu'],
                'dubs' => $data['dubs'],
                'subtitles' => $data['subtitles'],
            ],
        ]);
    }

    /**
     * Test if can log context on languageable creation failure.
     *
     * @return void
     */
    public function test_if_can_log_context_on_languageable_creation_failure(): void
    {
        $logServiceMock = Mockery::mock(LogServiceInterface::class);
        $logServiceMock->shouldReceive('withContext')
            ->once()
            ->with(
                Mockery::on(function (string $title) {
                    return $title === 'Failed to update a languageable.';
                }),
                Mockery::on(function (array $context) {
                    return isset($context['code'], $context['message'], $context['trace']);
                }),
            );

        $this->app->instance(LogServiceInterface::class, $logServiceMock);

        $exception = new Exception('Test exception', 500);
        $languageableServiceMock = Mockery::mock(LanguageableServiceInterface::class);
        $languageableServiceMock->shouldReceive('update')
            ->once()
            ->andThrow($exception);

        $this->app->instance(LanguageableServiceInterface::class, $languageableServiceMock);

        $this->putJson(route('languageables.update', $this->languageable), $this->getValidPayload())
            ->assertServerError()
            ->assertSee('Test exception');
    }

    /**
     * Tear down application tests.
     *
     * @return void
     */
    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
