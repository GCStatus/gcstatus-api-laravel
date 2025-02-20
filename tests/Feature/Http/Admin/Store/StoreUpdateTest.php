<?php

namespace Tests\Feature\Http\Admin\Store;

use Mockery;
use Exception;
use App\Models\{Store, User};
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyStore,
    HasDummyPermission,
};
use App\Contracts\Services\{
    LogServiceInterface,
    StoreServiceInterface,
};

class StoreUpdateTest extends BaseIntegrationTesting
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
     * The dummy store.
     *
     * @var \App\Models\Store
     */
    private Store $store;

    /**
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'view:stores',
        'update:stores',
    ];

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->store = $this->createDummyStore();

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

        $this->putJson(route('stores.update', $this->store), $this->getValidPayload())
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

        $this->putJson(route('stores.update', $this->store), $this->getValidPayload())->assertNotFound();
    }

    /**
     * Test if can update a store without payload.
     *
     * @return void
     */
    public function test_if_can_update_a_store_without_payload(): void
    {
        $this->putJson(route('stores.update', $this->store))->assertOk();
    }

    /**
     * Test if can't update the name to a duplicated store.
     *
     * @return void
     */
    public function test_if_cant_update_name_to_a_duplicated_store(): void
    {
        $store = $this->createDummyStore();

        $data = [
            'name' => $store->name,
        ];

        $this->putJson(route('stores.update', $this->store), $data)
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
                    return $title === 'Failed to update a store.';
                }),
                Mockery::on(function (array $context) {
                    return isset($context['code'], $context['message'], $context['trace']);
                }),
            );

        $this->app->instance(LogServiceInterface::class, $logServiceMock);

        $exception = new Exception('Test exception', 500);
        $storeServiceMock = Mockery::mock(StoreServiceInterface::class);
        $storeServiceMock->shouldReceive('update')
            ->once()
            ->andThrow($exception);

        $this->app->instance(StoreServiceInterface::class, $storeServiceMock);

        $this->putJson(route('stores.update', $this->store), $this->getValidPayload())
            ->assertServerError()
            ->assertSee('Test exception');
    }

    /**
     * Test if can ignore self "duplicated" store to update.
     *
     * @return void
     */
    public function test_if_can_ignore_self_duplicated_store_to_update(): void
    {
        $this->putJson(route('stores.update', $this->store), [
            'name' => $this->store->name,
        ])->assertOk();
    }

    /**
     * Test if can create a Store with valid payload.
     *
     * @return void
     */
    public function test_if_can_create_a_Store_with_valid_payload(): void
    {
        $this->putJson(route('stores.update', $this->store), $this->getValidPayload())->assertOk();
    }

    /**
     * Test if can save the store on database correctly.
     *
     * @return void
     */
    public function test_if_can_save_the_store_on_database_correctly(): void
    {
        $this->putJson(route('stores.update', $this->store), $data = $this->getValidPayload())->assertOk();

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
        $this->putJson(route('stores.update', $this->store), $this->getValidPayload())->assertOk()->assertJsonStructure([
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
        $this->putJson(route('stores.update', $this->store), $data = $this->getValidPayload())->assertOk()->assertJson([
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
