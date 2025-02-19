<?php

namespace Tests\Feature\Http\Admin\Publisher;

use Mockery;
use Exception;
use App\Models\User;
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyPublisher,
    HasDummyPermission,
};
use App\Contracts\Services\{
    LogServiceInterface,
    PublisherServiceInterface,
};

class PublisherStoreTest extends BaseIntegrationTesting
{
    use HasDummyPublisher;
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
        'view:publishers',
        'create:publishers',
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

        $this->postJson(route('publishers.store'), $this->getValidPayload())
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

        $this->postJson(route('publishers.store'), $this->getValidPayload())->assertNotFound();
    }

    /**
     * Test if can't create a publisher without payload.
     *
     * @return void
     */
    public function test_if_cant_create_a_publisher_without_payload(): void
    {
        $this->postJson(route('publishers.store'))->assertUnprocessable();
    }

    /**
     * Test if can throw correct invalid json keys.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_keys(): void
    {
        $this->postJson(route('publishers.store'))
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
        $this->postJson(route('publishers.store'))
            ->assertUnprocessable()
            ->assertInvalid(['name', 'acting'])
            ->assertSee('The acting field is required. (and 1 more error)');
    }

    /**
     * Test if can't create a duplicated publisher.
     *
     * @return void
     */
    public function test_if_cant_create_a_duplicated_publisher(): void
    {
        $publisher = $this->createDummyPublisher();

        $data = [
            'name' => $publisher->name,
            'acting' => fake()->boolean(),
        ];

        $this->postJson(route('publishers.store'), $data)
            ->assertUnprocessable()
            ->assertInvalid(['name'])
            ->assertSee('The name has already been taken.');
    }

    /**
     * Test if can log context on Publisher creation failure.
     *
     * @return void
     */
    public function test_if_can_log_context_on_Publisher_creation_failure(): void
    {
        $logServiceMock = Mockery::mock(LogServiceInterface::class);
        $logServiceMock->shouldReceive('withContext')
            ->once()
            ->with(
                Mockery::on(function (string $title) {
                    return $title === 'Failed to create a new publisher.';
                }),
                Mockery::on(function (array $context) {
                    return isset($context['code'], $context['message'], $context['trace']);
                }),
            );

        $this->app->instance(LogServiceInterface::class, $logServiceMock);

        $exception = new Exception('Test exception', 500);
        $publisherServiceMock = Mockery::mock(PublisherServiceInterface::class);
        $publisherServiceMock->shouldReceive('create')
            ->once()
            ->andThrow($exception);

        $this->app->instance(PublisherServiceInterface::class, $publisherServiceMock);

        $this->postJson(route('publishers.store'), $this->getValidPayload())
            ->assertServerError()
            ->assertSee('Test exception');
    }

    /**
     * Test if can create a publisher with valid payload.
     *
     * @return void
     */
    public function test_if_can_create_a_publisher_with_valid_payload(): void
    {
        $this->postJson(route('publishers.store'), $this->getValidPayload())->assertCreated();
    }

    /**
     * Test if can save the publisher on database correctly.
     *
     * @return void
     */
    public function test_if_can_save_the_publisher_on_database_correctly(): void
    {
        $this->postJson(route('publishers.store'), $data = $this->getValidPayload())->assertCreated();

        $this->assertDatabaseHas('publishers', [
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
        $this->postJson(route('publishers.store'), $this->getValidPayload())->assertCreated()->assertJsonStructure([
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
        $this->postJson(route('publishers.store'), $data = $this->getValidPayload())->assertCreated()->assertJson([
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
