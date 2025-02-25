<?php

namespace Tests\Feature\Http\Admin\Cracker;

use Mockery;
use Exception;
use App\Models\{Cracker, User};
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyCracker,
    HasDummyPermission,
};
use App\Contracts\Services\{
    LogServiceInterface,
    CrackerServiceInterface,
};

class CrackerUpdateTest extends BaseIntegrationTesting
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
     * The dummy cracker.
     *
     * @var \App\Models\Cracker
     */
    private Cracker $cracker;

    /**
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'view:crackers',
        'update:crackers',
    ];

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->cracker = $this->createDummyCracker();

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

        $this->putJson(route('crackers.update', $this->cracker), $this->getValidPayload())
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

        $this->putJson(route('crackers.update', $this->cracker), $this->getValidPayload())->assertNotFound();
    }

    /**
     * Test if can't update a cracker without payload.
     *
     * @return void
     */
    public function test_if_cant_update_a_cracker_without_payload(): void
    {
        $this->putJson(route('crackers.update', $this->cracker))->assertUnprocessable();
    }

    /**
     * Test if can throw correct invalid json keys.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_keys(): void
    {
        $this->putJson(route('crackers.update', $this->cracker))
            ->assertUnprocessable()
            ->assertInvalid(['name']);
    }

    /**
     * Test if can throw correct invalid json messages.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_messages(): void
    {
        $this->putJson(route('crackers.update', $this->cracker))
            ->assertUnprocessable()
            ->assertInvalid(['name'])
            ->assertSee('The name field is required.');
    }

    /**
     * Test if can't update the name to a duplicated cracker.
     *
     * @return void
     */
    public function test_if_cant_update_name_to_a_duplicated_cracker(): void
    {
        $cracker = $this->createDummyCracker();

        $data = [
            'name' => $cracker->name,
        ];

        $this->putJson(route('crackers.update', $this->cracker), $data)
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
                    return $title === 'Failed to update a cracker.';
                }),
                Mockery::on(function (array $context) {
                    return isset($context['code'], $context['message'], $context['trace']);
                }),
            );

        $this->app->instance(LogServiceInterface::class, $logServiceMock);

        $exception = new Exception('Test exception', 500);
        $crackerServiceMock = Mockery::mock(CrackerServiceInterface::class);
        $crackerServiceMock->shouldReceive('update')
            ->once()
            ->andThrow($exception);

        $this->app->instance(CrackerServiceInterface::class, $crackerServiceMock);

        $this->putJson(route('crackers.update', $this->cracker), $this->getValidPayload())
            ->assertServerError()
            ->assertSee('Test exception');
    }

    /**
     * Test if can ignore self "duplicated" cracker to update.
     *
     * @return void
     */
    public function test_if_can_ignore_self_duplicated_cracker_to_update(): void
    {
        $this->putJson(route('crackers.update', $this->cracker), [
            'name' => $this->cracker->name,
        ])->assertOk();
    }

    /**
     * Test if can create a cracker with valid payload.
     *
     * @return void
     */
    public function test_if_can_create_a_cracker_with_valid_payload(): void
    {
        $this->putJson(route('crackers.update', $this->cracker), $this->getValidPayload())->assertOk();
    }

    /**
     * Test if can save the cracker on database correctly.
     *
     * @return void
     */
    public function test_if_can_save_the_cracker_on_database_correctly(): void
    {
        $this->putJson(route('crackers.update', $this->cracker), $data = $this->getValidPayload())->assertOk();

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
        $this->putJson(route('crackers.update', $this->cracker), $this->getValidPayload())->assertOk()->assertJsonStructure([
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
        $this->putJson(route('crackers.update', $this->cracker), $data = $this->getValidPayload())->assertOk()->assertJson([
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
