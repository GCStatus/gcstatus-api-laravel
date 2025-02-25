<?php

namespace Tests\Feature\Http\Admin\MediaType;

use Mockery;
use Exception;
use App\Models\{MediaType, User};
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyMediaType,
    HasDummyPermission,
};
use App\Contracts\Services\{
    LogServiceInterface,
    MediaTypeServiceInterface,
};

class MediaTypeUpdateTest extends BaseIntegrationTesting
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
     * The dummy MediaType.
     *
     * @var \App\Models\MediaType
     */
    private MediaType $mediaType;

    /**
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'view:media-types',
        'update:media-types',
    ];

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->mediaType = $this->createDummyMediaType();

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

        $this->putJson(route('media-types.update', $this->mediaType), $this->getValidPayload())
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

        $this->putJson(route('media-types.update', $this->mediaType), $this->getValidPayload())->assertNotFound();
    }

    /**
     * Test if can't update a media type without payload.
     *
     * @return void
     */
    public function test_if_cant_update_a_media_type_without_payload(): void
    {
        $this->putJson(route('media-types.update', $this->mediaType))->assertUnprocessable();
    }

    /**
     * Test if can throw correct invalid json keys.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_keys(): void
    {
        $this->putJson(route('media-types.update', $this->mediaType))
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
        $this->putJson(route('media-types.update', $this->mediaType))
            ->assertUnprocessable()
            ->assertInvalid(['name'])
            ->assertSee('The name field is required.');
    }

    /**
     * Test if can't update the name to a duplicated media type.
     *
     * @return void
     */
    public function test_if_cant_update_name_to_a_duplicated_media_type(): void
    {
        $mediaType = $this->createDummyMediaType();

        $data = [
            'name' => $mediaType->name,
        ];

        $this->putJson(route('media-types.update', $this->mediaType), $data)
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
                    return $title === 'Failed to update a media type.';
                }),
                Mockery::on(function (array $context) {
                    return isset($context['code'], $context['message'], $context['trace']);
                }),
            );

        $this->app->instance(LogServiceInterface::class, $logServiceMock);

        $exception = new Exception('Test exception', 500);
        $mediaTypeServiceMock = Mockery::mock(MediaTypeServiceInterface::class);
        $mediaTypeServiceMock->shouldReceive('update')
            ->once()
            ->andThrow($exception);

        $this->app->instance(MediaTypeServiceInterface::class, $mediaTypeServiceMock);

        $this->putJson(route('media-types.update', $this->mediaType), $this->getValidPayload())
            ->assertServerError()
            ->assertSee('Test exception');
    }

    /**
     * Test if can ignore self "duplicated" media type to update.
     *
     * @return void
     */
    public function test_if_can_ignore_self_duplicated_media_type_to_update(): void
    {
        $this->putJson(route('media-types.update', $this->mediaType), [
            'name' => $this->mediaType->name,
        ])->assertOk();
    }

    /**
     * Test if can create a media type with valid payload.
     *
     * @return void
     */
    public function test_if_can_create_a_media_type_with_valid_payload(): void
    {
        $this->putJson(route('media-types.update', $this->mediaType), $this->getValidPayload())->assertOk();
    }

    /**
     * Test if can save the media type on database correctly.
     *
     * @return void
     */
    public function test_if_can_save_the_media_type_on_database_correctly(): void
    {
        $this->putJson(route('media-types.update', $this->mediaType), $data = $this->getValidPayload())->assertOk();

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
        $this->putJson(route('media-types.update', $this->mediaType), $this->getValidPayload())->assertOk()->assertJsonStructure([
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
        $this->putJson(route('media-types.update', $this->mediaType), $data = $this->getValidPayload())->assertOk()->assertJson([
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
