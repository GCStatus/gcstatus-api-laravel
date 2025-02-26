<?php

namespace Tests\Feature\Http\Admin\Galleriable;

use App\Contracts\Services\GalleriableServiceInterface;
use App\Contracts\Services\LogServiceInterface;
use Illuminate\Http\UploadedFile;
use App\Models\{Galleriable, User, Game, MediaType};
use Exception;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\Traits\{HasDummyDlc, HasDummyGame};
use Tests\Feature\Http\BaseIntegrationTesting;

class GalleriableStoreTest extends BaseIntegrationTesting
{
    use HasDummyDlc;
    use HasDummyGame;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The galleriable model.
     *
     * @var \App\Models\Game
     */
    private Game $game;

    /**
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'create:galleriables',
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

        $this->game = $this->createDummyGame();
    }

    /**
     * Get a valid url payload.
     *
     * @return array<string, mixed>
     */
    private function getValidUrlPayload(): array
    {
        return [
            's3' => false,
            'galleriable_id' => $this->game->id,
            'url' => 'https://placehold.co/600x600',
            'galleriable_type' => $this->game::class,
            'media_type_id' => MediaType::PHOTO_CONST_ID,
        ];
    }

    /**
     * Get a valid file payload.
     *
     * @return array<string, mixed>
     */
    private function getValidFilePayload(): array
    {
        return [
            's3' => true,
            'galleriable_id' => $this->game->id,
            'galleriable_type' => $this->game::class,
            'media_type_id' => MediaType::PHOTO_CONST_ID,
            'file' => UploadedFile::fake()->create('fake.png'),
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

        $this->postJson(route('galleriables.store'))
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

        $this->postJson(route('galleriables.store'))->assertNotFound();
    }

    /**
     * Test if can't create a galleriable without payload.
     *
     * @return void
     */
    public function test_if_cant_create_a_galleriable_without_payload(): void
    {
        $this->postJson(route('galleriables.store'))->assertUnprocessable();
    }

    /**
     * Test if can throw correct invalid json keys.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_keys(): void
    {
        $this->postJson(route('galleriables.store'))
            ->assertUnprocessable()
            ->assertInvalid(['s3', 'galleriable_id', 'galleriable_type', 'media_type_id']);
    }

    /**
     * Test if can throw correct invalid json messages.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_messages(): void
    {
        $this->postJson(route('galleriables.store'))
            ->assertUnprocessable()
            ->assertInvalid(['s3', 'galleriable_id', 'galleriable_type', 'media_type_id'])
            ->assertSee('The s3 field is required. (and 3 more errors)');
    }

    /**
     * Test if can't create for not allowed galleriable types.
     *
     * @return void
     */
    public function test_if_cant_create_for_not_allowed_galleriable_types(): void
    {
        $this->postJson(route('galleriables.store'), [
            's3' => false,
            'url' => 'https://placehold.co/600x600',
            'galleriable_id' => 1,
            'galleriable_type' => User::class,
            'media_type_id' => MediaType::PHOTO_CONST_ID,
        ])->assertUnprocessable()
            ->assertInvalid(['galleriable_type'])
            ->assertSee('The selected galleriable type is invalid.');

        $this->postJson(route('galleriables.store'), [
            's3' => false,
            'url' => 'https://placehold.co/600x600',
            'galleriable_id' => 1,
            'galleriable_type' => fake()->randomElement(Galleriable::ALLOWED_GALLERIABLES_TYPE),
            'media_type_id' => MediaType::PHOTO_CONST_ID,
        ])->assertCreated();
    }

    /**
     * Test if can't create a galleriable without url or file.
     *
     * @return void
     */
    public function test_if_cant_create_a_galleriable_without_url_or_file(): void
    {
        $this->postJson(route('galleriables.store'), ['s3' => false])
            ->assertUnprocessable()
            ->assertInvalid(['url', 'galleriable_id', 'galleriable_type', 'media_type_id'])
            ->assertSee('The url field is required when s3 is false.');

        $this->postJson(route('galleriables.store'), ['s3' => true])
            ->assertUnprocessable()
            ->assertInvalid(['file', 'galleriable_id', 'galleriable_type', 'media_type_id'])
            ->assertSee('The file field is required when s3 is true.');
    }

    /**
     * Test if can't create a galleriable with invalid url if is not s3.
     *
     * @return void
     */
    public function test_if_cant_create_a_galleriable_with_invalid_url_if_is_not_s3(): void
    {
        $this->postJson(route('galleriables.store'), [
            's3' => false,
            'url' => 'http://invalid.co',
        ])->assertUnprocessable()
            ->assertInvalid(['url', 'galleriable_id', 'galleriable_type', 'media_type_id'])
            ->assertSee('The url field must be a valid URL.');
    }

    /**
     * Test if can't create a galleriable with invalid file mime type if is s3.
     *
     * @return void
     */
    public function test_if_cant_create_a_galleriable_with_invalid_file_mime_type_if_is_s3(): void
    {
        $this->postJson(route('galleriables.store'), [
            's3' => true,
            'file' => UploadedFile::fake()->create('fake.pdf'),
        ])->assertUnprocessable()
            ->assertInvalid(['file', 'galleriable_id', 'galleriable_type', 'media_type_id'])
            ->assertSee('The file field must be a file of type: png, jpg, jpeg, gif, bmp, webp, mp4, mov.');
    }

    /**
     * Test if can't create a galleriable if file size is higher than allowed.
     *
     * @return void
     */
    public function test_if_cant_create_a_galleriable_if_file_size_is_higher_than_allowed(): void
    {
        $this->postJson(route('galleriables.store'), [
            's3' => true,
            'file' => UploadedFile::fake()->create('fake.png', 3 * 1024),
        ])->assertUnprocessable()
            ->assertInvalid(['file', 'galleriable_id', 'galleriable_type', 'media_type_id'])
            ->assertSee('The file field must not be greater than 2048 kilobytes.');
    }

    /**
     * Test if can create a galleriable with valid url payload.
     *
     * @return void
     */
    public function test_if_can_create_a_galleriable_with_valid_url_payload(): void
    {
        $this->postJson(route('galleriables.store'), $this->getValidUrlPayload())->assertCreated();
    }

    /**
     * Test if can create a galleriable with valid file payload.
     *
     * @return void
     */
    public function test_if_can_create_a_galleriable_with_valid_file_payload(): void
    {
        $this->postJson(route('galleriables.store'), $this->getValidFilePayload())->assertCreated();
    }

    /**
     * Test if can save correctly a galleriable with valid url payload.
     *
     * @return void
     */
    public function test_if_can_save_correctly_a_galleriable_with_valid_url_payload(): void
    {
        $this->postJson(route('galleriables.store'), $data = $this->getValidUrlPayload())->assertCreated();

        $this->assertDatabaseHas('galleriables', [
            's3' => $data['s3'],
            'path' => $data['url'],
            'media_type_id' => $data['media_type_id'],
            'galleriable_id' => $data['galleriable_id'],
            'galleriable_type' => $data['galleriable_type'],
        ]);
    }

    /**
     * Test if can save correctly a galleriable with valid file payload.
     *
     * @return void
     */
    public function test_if_can_save_correctly_a_galleriable_with_valid_file_payload(): void
    {
        $this->postJson(route('galleriables.store'), $data = $this->getValidFilePayload())->assertCreated();

        /** @var \Illuminate\Http\UploadedFile $file */
        $file = $data['file'];

        $this->assertDatabaseHas('galleriables', [
            's3' => $data['s3'],
            'path' => 'games/' . $file->hashName(),
            'media_type_id' => $data['media_type_id'],
            'galleriable_id' => $data['galleriable_id'],
            'galleriable_type' => $data['galleriable_type'],
        ]);
    }

    /**
     * Test if can return correctly response with valid url payload.
     *
     * @return void
     */
    public function test_if_can_return_correctly_response_with_valid_url_payload(): void
    {
        $this->postJson(route('galleriables.store'), $data = $this->getValidUrlPayload())->assertCreated()->assertJson([
            'data' => [
                'path' => $data['url'],
            ],
        ]);
    }

    /**
     * Test if can return correctly response with valid file payload.
     *
     * @return void
     */
    public function test_if_can_return_correctly_response_with_valid_file_payload(): void
    {
        $data = $this->getValidFilePayload();

        /** @var \Illuminate\Http\UploadedFile $file */
        $file = $data['file'];

        $path = storage()->getPath('games/' . $file->hashName());

        $this->postJson(route('galleriables.store'), $data)->assertCreated()->assertJson([
            'data' => [
                'path' => $path,
            ],
        ]);
    }

    /**
     * Test if can save correctly the file on storage.
     *
     * @return void
     */
    public function test_if_can_save_correctly_the_file_on_storage(): void
    {
        $data = $this->getValidFilePayload();

        /** @var \Illuminate\Http\UploadedFile $file */
        $file = $data['file'];

        $path = 'games/' . $file->hashName();

        Storage::assertMissing($path);

        $this->postJson(route('galleriables.store'), $data)->assertCreated();

        Storage::assertExists($path);
    }

    /**
     * Test if can get correctly json structure.
     *
     * @return void
     */
    public function test_if_can_get_correctly_json_structure(): void
    {
        $this->postJson(route('galleriables.store'), $this->getValidUrlPayload())->assertCreated()->assertJsonStructure([
            'data' => [
                'id',
                'path',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    /**
     * Test if can log context on galleriable creation failure.
     *
     * @return void
     */
    public function test_if_can_log_context_on_galleriable_creation_failure(): void
    {
        $logServiceMock = Mockery::mock(LogServiceInterface::class);
        $logServiceMock->shouldReceive('withContext')
            ->once()
            ->with(
                Mockery::on(function (string $title) {
                    return $title === 'Failed to create a new galleriable.';
                }),
                Mockery::on(function (array $context) {
                    return isset($context['code'], $context['message'], $context['trace']);
                }),
            );

        $this->app->instance(LogServiceInterface::class, $logServiceMock);

        $exception = new Exception('Test exception', 500);
        $galleriableServiceMock = Mockery::mock(GalleriableServiceInterface::class);
        $galleriableServiceMock->shouldReceive('create')
            ->once()
            ->andThrow($exception);

        $this->app->instance(GalleriableServiceInterface::class, $galleriableServiceMock);

        $this->postJson(route('galleriables.store'), $this->getValidUrlPayload())
            ->assertServerError()
            ->assertSee('Test exception');
    }
}
