<?php

namespace Tests\Feature\Http\Admin\Developer;

use Mockery;
use Exception;
use App\Models\User;
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyDeveloper,
    HasDummyPermission,
};
use App\Contracts\Services\{
    LogServiceInterface,
    DeveloperServiceInterface,
};

class DeveloperStoreTest extends BaseIntegrationTesting
{
    use HasDummyDeveloper;
    use HasDummyPermission;

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
        'view:developers',
        'create:developers',
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
     * Get a valid payload.
     *
     * @return array<string, mixed>
     */
    private function getValidPayload(): array
    {
        return [
            'name' => fake()->word(),
            'acting' => fake()->boolean(),
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

        $this->postJson(route('developers.store'), $this->getValidPayload())
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

        $this->postJson(route('developers.store'), $this->getValidPayload())->assertNotFound();
    }

    /**
     * Test if can't create a Developer without payload.
     *
     * @return void
     */
    public function test_if_cant_create_a_Developer_without_payload(): void
    {
        $this->postJson(route('developers.store'))->assertUnprocessable();
    }

    /**
     * Test if can throw correct invalid json keys.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_keys(): void
    {
        $this->postJson(route('developers.store'))
            ->assertUnprocessable()
            ->assertInvalid(['name', 'acting']);
    }

    /**
     * Test if can throw correct invalid json messages.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_messages(): void
    {
        $this->postJson(route('developers.store'))
            ->assertUnprocessable()
            ->assertInvalid(['name', 'acting'])
            ->assertSee('The acting field is required. (and 1 more error)');
    }

    /**
     * Test if can't create a duplicated Developer.
     *
     * @return void
     */
    public function test_if_cant_create_a_duplicated_Developer(): void
    {
        $developer = $this->createDummyDeveloper();

        $data = [
            'name' => $developer->name,
            'acting' => fake()->boolean(),
        ];

        $this->postJson(route('developers.store'), $data)
            ->assertUnprocessable()
            ->assertInvalid(['name'])
            ->assertSee('The name has already been taken.');
    }

    /**
     * Test if can log context on Developer creation failure.
     *
     * @return void
     */
    public function test_if_can_log_context_on_Developer_creation_failure(): void
    {
        $logServiceMock = Mockery::mock(LogServiceInterface::class);
        $logServiceMock->shouldReceive('withContext')
            ->once()
            ->with(
                Mockery::on(function (string $title) {
                    return $title === 'Failed to create a new developer.';
                }),
                Mockery::on(function (array $context) {
                    return isset($context['code'], $context['message'], $context['trace']);
                }),
            );

        $this->app->instance(LogServiceInterface::class, $logServiceMock);

        $exception = new Exception('Test exception', 500);
        $developerServiceMock = Mockery::mock(developerServiceInterface::class);
        $developerServiceMock->shouldReceive('create')
            ->once()
            ->andThrow($exception);

        $this->app->instance(developerServiceInterface::class, $developerServiceMock);

        $this->postJson(route('developers.store'), $this->getValidPayload())
            ->assertServerError()
            ->assertSee('Test exception');
    }

    /**
     * Test if can create a developer with valid payload.
     *
     * @return void
     */
    public function test_if_can_create_a_developer_with_valid_payload(): void
    {
        $this->postJson(route('developers.store'), $this->getValidPayload())->assertCreated();
    }

    /**
     * Test if can save the developer on database correctly.
     *
     * @return void
     */
    public function test_if_can_save_the_developer_on_database_correctly(): void
    {
        $this->postJson(route('developers.store'), $data = $this->getValidPayload())->assertCreated();

        $this->assertDatabaseHas('developers', [
            'name' => $data['name'],
            'acting' => $data['acting'],
        ]);
    }

    /**
     * Test if can get correct json structure response.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_structure_response(): void
    {
        $this->postJson(route('developers.store'), $this->getValidPayload())->assertCreated()->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'slug',
                'acting',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    /**
     * Test if can get correct json structure data.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_structure_data(): void
    {
        $this->postJson(route('developers.store'), $data = $this->getValidPayload())->assertCreated()->assertJson([
            'data' => [
                'name' => $data['name'],
                'acting' => $data['acting'],
            ],
        ]);
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
