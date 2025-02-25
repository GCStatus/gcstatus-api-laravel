<?php

namespace Tests\Feature\Http\Admin\MediaType;

use Mockery;
use Exception;
use App\Models\User;
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyMediaType,
    HasDummyPermission,
};
use App\Contracts\Services\{
    LogServiceInterface,
    MediaTypeServiceInterface,
};

class MediaTypeStoreTest extends BaseIntegrationTesting
{
    use HasDummyMediaType;
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
        'view:media-types',
        'create:media-types',
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

        $this->postJson(route('media-types.store'), $this->getValidPayload())
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

        $this->postJson(route('media-types.store'), $this->getValidPayload())->assertNotFound();
    }

    /**
     * Test if can't create a media type without payload.
     *
     * @return void
     */
    public function test_if_cant_create_a_media_type_without_payload(): void
    {
        $this->postJson(route('media-types.store'))->assertUnprocessable();
    }

    /**
     * Test if can throw correct invalid json keys.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_keys(): void
    {
        $this->postJson(route('media-types.store'))
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
        $this->postJson(route('media-types.store'))
            ->assertUnprocessable()
            ->assertInvalid(['name'])
            ->assertSee('The name field is required.');
    }

    /**
     * Test if can't create a duplicated media type.
     *
     * @return void
     */
    public function test_if_cant_create_a_duplicated_media_type(): void
    {
        $MediaType = $this->createDummyMediaType();

        $data = [
            'name' => $MediaType->name,
        ];

        $this->postJson(route('media-types.store'), $data)
            ->assertUnprocessable()
            ->assertInvalid(['name'])
            ->assertSee('The name has already been taken.');
    }

    /**
     * Test if can log context on MediaType creation failure.
     *
     * @return void
     */
    public function test_if_can_log_context_on_media_type_creation_failure(): void
    {
        $logServiceMock = Mockery::mock(LogServiceInterface::class);
        $logServiceMock->shouldReceive('withContext')
            ->once()
            ->with(
                Mockery::on(function (string $title) {
                    return $title === 'Failed to create a new media type.';
                }),
                Mockery::on(function (array $context) {
                    return isset($context['code'], $context['message'], $context['trace']);
                }),
            );

        $this->app->instance(LogServiceInterface::class, $logServiceMock);

        $exception = new Exception('Test exception', 500);
        $MediaTypeserviceMock = Mockery::mock(MediaTypeServiceInterface::class);
        $MediaTypeserviceMock->shouldReceive('create')
            ->once()
            ->andThrow($exception);

        $this->app->instance(MediaTypeServiceInterface::class, $MediaTypeserviceMock);

        $this->postJson(route('media-types.store'), $this->getValidPayload())
            ->assertServerError()
            ->assertSee('Test exception');
    }

    /**
     * Test if can create a media type with valid payload.
     *
     * @return void
     */
    public function test_if_can_create_a_media_type_with_valid_payload(): void
    {
        $this->postJson(route('media-types.store'), $this->getValidPayload())->assertCreated();
    }

    /**
     * Test if can save the media type on database correctly.
     *
     * @return void
     */
    public function test_if_can_save_the_media_type_on_database_correctly(): void
    {
        $this->postJson(route('media-types.store'), $data = $this->getValidPayload())->assertCreated();

        $this->assertDatabaseHas('media_types', [
            'name' => $data['name'],
        ]);
    }

    /**
     * Test if can get correct json structure response.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_structure_response(): void
    {
        $this->postJson(route('media-types.store'), $this->getValidPayload())->assertCreated()->assertJsonStructure([
            'data' => [
                'id',
                'name',
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
        $this->postJson(route('media-types.store'), $data = $this->getValidPayload())->assertCreated()->assertJson([
            'data' => [
                'name' => $data['name'],
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
