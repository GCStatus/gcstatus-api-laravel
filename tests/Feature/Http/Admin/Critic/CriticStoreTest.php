<?php

namespace Tests\Feature\Http\Admin\Critic;

use Mockery;
use Exception;
use App\Models\User;
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyCritic,
    HasDummyPermission,
};
use App\Contracts\Services\{
    LogServiceInterface,
    CriticServiceInterface,
};

class CriticStoreTest extends BaseIntegrationTesting
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
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'view:critics',
        'create:critics',
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

        $this->postJson(route('critics.store'), $this->getValidPayload())
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

        $this->postJson(route('critics.store'), $this->getValidPayload())->assertNotFound();
    }

    /**
     * Test if can't create a Critic without payload.
     *
     * @return void
     */
    public function test_if_cant_create_a_Critic_without_payload(): void
    {
        $this->postJson(route('critics.store'))->assertUnprocessable();
    }

    /**
     * Test if can throw correct invalid json keys.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_keys(): void
    {
        $this->postJson(route('critics.store'))
            ->assertUnprocessable()
            ->assertInvalid(['url', 'name', 'logo', 'acting']);
    }

    /**
     * Test if can throw correct invalid json messages.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_messages(): void
    {
        $this->postJson(route('critics.store'))
            ->assertUnprocessable()
            ->assertInvalid(['url', 'name', 'logo', 'acting'])
            ->assertSee('The acting field is required. (and 3 more errors)');
    }

    /**
     * Test if can't create a duplicated critic.
     *
     * @return void
     */
    public function test_if_cant_create_a_duplicated_critic(): void
    {
        $critic = $this->createDummyCritic();

        $data = [
            ...$this->getValidPayload(),
            'name' => $critic->name,
        ];

        $this->postJson(route('critics.store'), $data)
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
                    return $title === 'Failed to create a new critic.';
                }),
                Mockery::on(function (array $context) {
                    return isset($context['code'], $context['message'], $context['trace']);
                }),
            );

        $this->app->instance(LogServiceInterface::class, $logServiceMock);

        $exception = new Exception('Test exception', 500);
        $criticServiceMock = Mockery::mock(CriticServiceInterface::class);
        $criticServiceMock->shouldReceive('create')
            ->once()
            ->andThrow($exception);

        $this->app->instance(CriticServiceInterface::class, $criticServiceMock);

        $this->postJson(route('critics.store'), $this->getValidPayload())
            ->assertServerError()
            ->assertSee('Test exception');
    }

    /**
     * Test if can create a critic with valid payload.
     *
     * @return void
     */
    public function test_if_can_create_a_critic_with_valid_payload(): void
    {
        $this->postJson(route('critics.store'), $this->getValidPayload())->assertCreated();
    }

    /**
     * Test if can save the critic on database correctly.
     *
     * @return void
     */
    public function test_if_can_save_the_critic_on_database_correctly(): void
    {
        $this->postJson(route('critics.store'), $data = $this->getValidPayload())->assertCreated();

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
        $this->postJson(route('critics.store'), $this->getValidPayload())->assertCreated()->assertJsonStructure([
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
        $this->postJson(route('critics.store'), $data = $this->getValidPayload())->assertCreated()->assertJson([
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
