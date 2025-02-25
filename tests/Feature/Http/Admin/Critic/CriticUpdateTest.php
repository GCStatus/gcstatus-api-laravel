<?php

namespace Tests\Feature\Http\Admin\Critic;

use Mockery;
use Exception;
use App\Models\{Critic, User};
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyCritic,
    HasDummyPermission,
};
use App\Contracts\Services\{
    LogServiceInterface,
    CriticServiceInterface,
};

class CriticUpdateTest extends BaseIntegrationTesting
{
    use HasDummyCritic;
    use HasDummyPermission;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The dummy critic.
     *
     * @var \App\Models\Critic
     */
    private Critic $critic;

    /**
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'view:critics',
        'update:critics',
    ];

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->critic = $this->createDummyCritic();

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
            'url' => 'https://google.com',
            'logo' => 'https://placehold.co/600x400/EEE/31343C',
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

        $this->putJson(route('critics.update', $this->critic), $this->getValidPayload())
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

        $this->putJson(route('critics.update', $this->critic), $this->getValidPayload())->assertNotFound();
    }

    /**
     * Test if can update a critic without payload.
     *
     * @return void
     */
    public function test_if_can_update_a_critic_without_payload(): void
    {
        $this->putJson(route('critics.update', $this->critic))->assertOk();
    }

    /**
     * Test if can't update the name to a duplicated critic.
     *
     * @return void
     */
    public function test_if_cant_update_name_to_a_duplicated_critic(): void
    {
        $critic = $this->createDummyCritic();

        $data = [
            'name' => $critic->name,
        ];

        $this->putJson(route('critics.update', $this->critic), $data)
            ->assertUnprocessable()
            ->assertInvalid(['name'])
            ->assertSee('The name has already been taken.');
    }

    /**
     * Test if can log context on critic creation failure.
     *
     * @return void
     */
    public function test_if_can_log_context_on_critic_creation_failure(): void
    {
        $logServiceMock = Mockery::mock(LogServiceInterface::class);
        $logServiceMock->shouldReceive('withContext')
            ->once()
            ->with(
                Mockery::on(function (string $title) {
                    return $title === 'Failed to update a critic.';
                }),
                Mockery::on(function (array $context) {
                    return isset($context['code'], $context['message'], $context['trace']);
                }),
            );

        $this->app->instance(LogServiceInterface::class, $logServiceMock);

        $exception = new Exception('Test exception', 500);
        $criticServiceMock = Mockery::mock(CriticServiceInterface::class);
        $criticServiceMock->shouldReceive('update')
            ->once()
            ->andThrow($exception);

        $this->app->instance(CriticServiceInterface::class, $criticServiceMock);

        $this->putJson(route('critics.update', $this->critic), $this->getValidPayload())
            ->assertServerError()
            ->assertSee('Test exception');
    }

    /**
     * Test if can ignore self "duplicated" critic to update.
     *
     * @return void
     */
    public function test_if_can_ignore_self_duplicated_critic_to_update(): void
    {
        $this->putJson(route('critics.update', $this->critic), [
            'name' => $this->critic->name,
        ])->assertOk();
    }

    /**
     * Test if can create a critic with valid payload.
     *
     * @return void
     */
    public function test_if_can_create_a_critic_with_valid_payload(): void
    {
        $this->putJson(route('critics.update', $this->critic), $this->getValidPayload())->assertOk();
    }

    /**
     * Test if can save the critic on database correctly.
     *
     * @return void
     */
    public function test_if_can_save_the_critic_on_database_correctly(): void
    {
        $this->putJson(route('critics.update', $this->critic), $data = $this->getValidPayload())->assertOk();

        $this->assertDatabaseHas('critics', [
            'url' => $data['url'],
            'name' => $data['name'],
            'logo' => $data['logo'],
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
        $this->putJson(route('critics.update', $this->critic), $this->getValidPayload())->assertOk()->assertJsonStructure([
            'data' => [
                'id',
                'url',
                'name',
                'slug',
                'logo',
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
        $this->putJson(route('critics.update', $this->critic), $data = $this->getValidPayload())->assertOk()->assertJson([
            'data' => [
                'url' => $data['url'],
                'name' => $data['name'],
                'logo' => $data['logo'],
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
