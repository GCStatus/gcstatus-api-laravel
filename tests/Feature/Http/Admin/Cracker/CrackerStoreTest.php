<?php

namespace Tests\Feature\Http\Admin\Cracker;

use Mockery;
use Exception;
use App\Models\User;
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyCracker,
    HasDummyPermission,
};
use App\Contracts\Services\{
    LogServiceInterface,
    CrackerServiceInterface,
};

class CrackerStoreTest extends BaseIntegrationTesting
{
    use HasDummyCracker;
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
        'view:crackers',
        'create:crackers',
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

        $this->postJson(route('crackers.store'), $this->getValidPayload())
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

        $this->postJson(route('crackers.store'), $this->getValidPayload())->assertNotFound();
    }

    /**
     * Test if can't create a Cracker without payload.
     *
     * @return void
     */
    public function test_if_cant_create_a_Cracker_without_payload(): void
    {
        $this->postJson(route('crackers.store'))->assertUnprocessable();
    }

    /**
     * Test if can throw correct invalid json keys.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_keys(): void
    {
        $this->postJson(route('crackers.store'))
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
        $this->postJson(route('crackers.store'))
            ->assertUnprocessable()
            ->assertInvalid(['name', 'acting'])
            ->assertSee('The acting field is required. (and 1 more error)');
    }

    /**
     * Test if can't create a duplicated cracker.
     *
     * @return void
     */
    public function test_if_cant_create_a_duplicated_cracker(): void
    {
        $cracker = $this->createDummyCracker();

        $data = [
            'name' => $cracker->name,
            'acting' => fake()->boolean(),
        ];

        $this->postJson(route('crackers.store'), $data)
            ->assertUnprocessable()
            ->assertInvalid(['name'])
            ->assertSee('The name has already been taken.');
    }

    /**
     * Test if can log context on cracker creation failure.
     *
     * @return void
     */
    public function test_if_can_log_context_on_cracker_creation_failure(): void
    {
        $logServiceMock = Mockery::mock(LogServiceInterface::class);
        $logServiceMock->shouldReceive('withContext')
            ->once()
            ->with(
                Mockery::on(function (string $title) {
                    return $title === 'Failed to create a new cracker.';
                }),
                Mockery::on(function (array $context) {
                    return isset($context['code'], $context['message'], $context['trace']);
                }),
            );

        $this->app->instance(LogServiceInterface::class, $logServiceMock);

        $exception = new Exception('Test exception', 500);
        $crackerServiceMock = Mockery::mock(CrackerServiceInterface::class);
        $crackerServiceMock->shouldReceive('create')
            ->once()
            ->andThrow($exception);

        $this->app->instance(CrackerServiceInterface::class, $crackerServiceMock);

        $this->postJson(route('crackers.store'), $this->getValidPayload())
            ->assertServerError()
            ->assertSee('Test exception');
    }

    /**
     * Test if can create a cracker with valid payload.
     *
     * @return void
     */
    public function test_if_can_create_a_cracker_with_valid_payload(): void
    {
        $this->postJson(route('crackers.store'), $this->getValidPayload())->assertCreated();
    }

    /**
     * Test if can save the cracker on database correctly.
     *
     * @return void
     */
    public function test_if_can_save_the_cracker_on_database_correctly(): void
    {
        $this->postJson(route('crackers.store'), $data = $this->getValidPayload())->assertCreated();

        $this->assertDatabaseHas('crackers', [
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
        $this->postJson(route('crackers.store'), $this->getValidPayload())->assertCreated()->assertJsonStructure([
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
        $this->postJson(route('crackers.store'), $data = $this->getValidPayload())->assertCreated()->assertJson([
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
