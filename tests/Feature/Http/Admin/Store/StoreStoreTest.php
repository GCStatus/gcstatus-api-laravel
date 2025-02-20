<?php

namespace Tests\Feature\Http\Admin\Store;

use Mockery;
use Exception;
use App\Models\User;
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyStore,
    HasDummyPermission,
};
use App\Contracts\Services\{
    LogServiceInterface,
    StoreServiceInterface,
};

class StoreStoreTest extends BaseIntegrationTesting
{
    use HasDummyStore;
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
        'view:stores',
        'create:stores',
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
     * @return array<string, string>
     */
    private function getValidPayload(): array
    {
        return [
            'name' => fake()->word(),
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

        $this->postJson(route('stores.store'), $this->getValidPayload())
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

        $this->postJson(route('stores.store'), $this->getValidPayload())->assertNotFound();
    }

    /**
     * Test if can't create a Store without payload.
     *
     * @return void
     */
    public function test_if_cant_create_a_Store_without_payload(): void
    {
        $this->postJson(route('stores.store'))->assertUnprocessable();
    }

    /**
     * Test if can throw correct invalid json keys.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_keys(): void
    {
        $this->postJson(route('stores.store'))
            ->assertUnprocessable()
            ->assertInvalid(['name', 'url', 'logo']);
    }

    /**
     * Test if can throw correct invalid json messages.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_messages(): void
    {
        $this->postJson(route('stores.store'))
            ->assertUnprocessable()
            ->assertInvalid(['name'])
            ->assertSee('The url field is required. (and 2 more errors)');
    }

    /**
     * Test if can't create a duplicated store.
     *
     * @return void
     */
    public function test_if_cant_create_a_duplicated_store(): void
    {
        $store = $this->createDummyStore();

        $data = [
            'name' => $store->name,
        ];

        $this->postJson(route('stores.store'), $data)
            ->assertUnprocessable()
            ->assertInvalid(['name'])
            ->assertSee('The name has already been taken.');
    }

    /**
     * Test if can log context on store creation failure.
     *
     * @return void
     */
    public function test_if_can_log_context_on_store_creation_failure(): void
    {
        $logServiceMock = Mockery::mock(LogServiceInterface::class);
        $logServiceMock->shouldReceive('withContext')
            ->once()
            ->with(
                Mockery::on(function (string $title) {
                    return $title === 'Failed to create a new store.';
                }),
                Mockery::on(function (array $context) {
                    return isset($context['code'], $context['message'], $context['trace']);
                }),
            );

        $this->app->instance(LogServiceInterface::class, $logServiceMock);

        $exception = new Exception('Test exception', 500);
        $storeServiceMock = Mockery::mock(StoreServiceInterface::class);
        $storeServiceMock->shouldReceive('create')
            ->once()
            ->andThrow($exception);

        $this->app->instance(StoreServiceInterface::class, $storeServiceMock);

        $this->postJson(route('stores.store'), $this->getValidPayload())
            ->assertServerError()
            ->assertSee('Test exception');
    }

    /**
     * Test if can create a store with valid payload.
     *
     * @return void
     */
    public function test_if_can_create_a_store_with_valid_payload(): void
    {
        $this->postJson(route('stores.store'), $this->getValidPayload())->assertCreated();
    }

    /**
     * Test if can save the store on database correctly.
     *
     * @return void
     */
    public function test_if_can_save_the_store_on_database_correctly(): void
    {
        $this->postJson(route('stores.store'), $data = $this->getValidPayload())->assertCreated();

        $this->assertDatabaseHas('stores', [
            'url' => $data['url'],
            'name' => $data['name'],
            'logo' => $data['logo'],
        ]);
    }

    /**
     * Test if can get correct json structure response.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_structure_response(): void
    {
        $this->postJson(route('stores.store'), $this->getValidPayload())->assertCreated()->assertJsonStructure([
            'data' => [
                'id',
                'url',
                'name',
                'slug',
                'logo',
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
        $this->postJson(route('stores.store'), $data = $this->getValidPayload())->assertCreated()->assertJson([
            'data' => [
                'url' => $data['url'],
                'name' => $data['name'],
                'logo' => $data['logo'],
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
