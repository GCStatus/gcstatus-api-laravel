<?php

namespace Tests\Feature\Http\Admin\Languageable;

use Mockery;
use Exception;
use Tests\Traits\HasDummyLanguage;
use App\Models\{User, Languageable};
use Tests\Feature\Http\BaseIntegrationTesting;
use App\Contracts\Services\{LogServiceInterface, LanguageableServiceInterface};

class LanguageableStoreTest extends BaseIntegrationTesting
{
    use HasDummyLanguage;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'create:languageables',
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
    }

    /**
     * Get valid payload.
     *
     * @return array<string, mixed>
     */
    private function getValidPayload(): array
    {
        return [
            'languageable_id' => 1,
            'menu' => fake()->boolean(),
            'dubs' => fake()->boolean(),
            'subtitles' => fake()->boolean(),
            'language_id' => $this->createDummyLanguage()->id,
            'languageable_type' => fake()->randomElement(Languageable::ALLOWED_LANGUAGEABLE_TYPES),
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

        $this->postJson(route('languageables.store'), $this->getValidPayload())
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

        $this->postJson(route('languageables.store'), $this->getValidPayload())->assertNotFound();
    }

    /**
     * Test if can't create a languageable without payload.
     *
     * @return void
     */
    public function test_if_cant_create_a_languageable_without_payload(): void
    {
        $this->postJson(route('languageables.store'))->assertUnprocessable();
    }

    /**
     * Test if can throw correct invalid json keys.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_keys(): void
    {
        $this->postJson(route('languageables.store'))
            ->assertUnprocessable()
            ->assertInvalid(['menu', 'dubs', 'subtitles', 'language_id', 'languageable_id', 'languageable_type']);
    }

    /**
     * Test if can throw correct invalid json messages.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_messages(): void
    {
        $this->postJson(route('languageables.store'))
            ->assertUnprocessable()
            ->assertInvalid(['menu', 'dubs', 'subtitles', 'language_id', 'languageable_id', 'languageable_type'])
            ->assertSee('The menu field is required. (and 5 more errors)');
    }

    /**
     * Test if can't create a languageable for invalid languageable type.
     *
     * @return void
     */
    public function test_if_cant_create_a_languageable_for_invalid_languageable_type(): void
    {
        $this->postJson(route('languageables.store'), [
            'languageable_id' => 1,
            'menu' => fake()->boolean(),
            'dubs' => fake()->boolean(),
            'subtitles' => fake()->boolean(),
            'languageable_type' => User::class,
            'language_id' => $this->createDummyLanguage()->id,
        ])->assertUnprocessable()
            ->assertInvalid(['languageable_type'])
            ->assertSee('The selected languageable type is invalid.');
    }

    /**
     * Test if can create languageable with valid payload.
     *
     * @return void
     */
    public function test_if_can_create_languageable_with_valid_payload(): void
    {
        $this->postJson(route('languageables.store'), $this->getValidPayload())->assertCreated();
    }

    /**
     * Test if can save new languageable on database.
     *
     * @return void
     */
    public function test_if_can_save_new_languageable_on_database(): void
    {
        $this->postJson(route('languageables.store'), $data = $this->getValidPayload())->assertCreated();

        $this->assertDatabaseHas('languageables', [
            'menu' => $data['menu'],
            'dubs' => $data['dubs'],
            'subtitles' => $data['subtitles'],
            'language_id' => $data['language_id'],
            'languageable_id' => $data['languageable_id'],
            'languageable_type' => $data['languageable_type'],
        ]);
    }

    /**
     * Test if can get correct json structure on response.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_structure_on_response(): void
    {
        $this->postJson(route('languageables.store'), $this->getValidPayload())->assertCreated()->assertJsonStructure([
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
        $this->postJson(route('languageables.store'), $data = $this->getValidPayload())->assertCreated()->assertJson([
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
                    return $title === 'Failed to create a new languageable.';
                }),
                Mockery::on(function (array $context) {
                    return isset($context['code'], $context['message'], $context['trace']);
                }),
            );

        $this->app->instance(LogServiceInterface::class, $logServiceMock);

        $exception = new Exception('Test exception', 500);
        $languageableServiceMock = Mockery::mock(LanguageableServiceInterface::class);
        $languageableServiceMock->shouldReceive('create')
            ->once()
            ->andThrow($exception);

        $this->app->instance(LanguageableServiceInterface::class, $languageableServiceMock);

        $this->postJson(route('languageables.store'), $this->getValidPayload())
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
