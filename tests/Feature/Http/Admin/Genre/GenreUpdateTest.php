<?php

namespace Tests\Feature\Http\Admin\Genre;

use Mockery;
use Exception;
use App\Models\{Genre, User};
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyGenre,
    HasDummyPermission,
};
use App\Contracts\Services\{
    LogServiceInterface,
    GenreServiceInterface,
};

class GenreUpdateTest extends BaseIntegrationTesting
{
    use HasDummyGenre;
    use HasDummyPermission;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The dummy genre.
     *
     * @var \App\Models\Genre
     */
    private Genre $genre;

    /**
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'view:genres',
        'update:genres',
    ];

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->genre = $this->createDummyGenre();

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

        $this->putJson(route('genres.update', $this->genre), $this->getValidPayload())
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

        $this->putJson(route('genres.update', $this->genre), $this->getValidPayload())->assertNotFound();
    }

    /**
     * Test if can't update a genre without payload.
     *
     * @return void
     */
    public function test_if_cant_update_a_genre_without_payload(): void
    {
        $this->putJson(route('genres.update', $this->genre))->assertUnprocessable();
    }

    /**
     * Test if can throw correct invalid json keys.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_keys(): void
    {
        $this->putJson(route('genres.update', $this->genre))
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
        $this->putJson(route('genres.update', $this->genre))
            ->assertUnprocessable()
            ->assertInvalid(['name'])
            ->assertSee('The name field is required.');
    }

    /**
     * Test if can't update the name to a duplicated genre.
     *
     * @return void
     */
    public function test_if_cant_update_name_to_a_duplicated_genre(): void
    {
        $Genre = $this->createDummyGenre();

        $data = [
            'name' => $Genre->name,
        ];

        $this->putJson(route('genres.update', $this->genre), $data)
            ->assertUnprocessable()
            ->assertInvalid(['name'])
            ->assertSee('The name has already been taken.');
    }

    /**
     * Test if can log context on genre creation failure.
     *
     * @return void
     */
    public function test_if_can_log_context_on_genre_creation_failure(): void
    {
        $logServiceMock = Mockery::mock(LogServiceInterface::class);
        $logServiceMock->shouldReceive('withContext')
            ->once()
            ->with(
                Mockery::on(function (string $title) {
                    return $title === 'Failed to update a genre.';
                }),
                Mockery::on(function (array $context) {
                    return isset($context['code'], $context['message'], $context['trace']);
                }),
            );

        $this->app->instance(LogServiceInterface::class, $logServiceMock);

        $exception = new Exception('Test exception', 500);
        $genreServiceMock = Mockery::mock(GenreServiceInterface::class);
        $genreServiceMock->shouldReceive('update')
            ->once()
            ->andThrow($exception);

        $this->app->instance(GenreServiceInterface::class, $genreServiceMock);

        $this->putJson(route('genres.update', $this->genre), $this->getValidPayload())
            ->assertServerError()
            ->assertSee('Test exception');
    }

    /**
     * Test if can ignore self "duplicated" genre to update.
     *
     * @return void
     */
    public function test_if_can_ignore_self_duplicated_genre_to_update(): void
    {
        $this->putJson(route('genres.update', $this->genre), [
            'name' => $this->genre->name,
        ])->assertOk();
    }

    /**
     * Test if can create a genre with valid payload.
     *
     * @return void
     */
    public function test_if_can_create_a_genre_with_valid_payload(): void
    {
        $this->putJson(route('genres.update', $this->genre), $this->getValidPayload())->assertOk();
    }

    /**
     * Test if can save the genre on database correctly.
     *
     * @return void
     */
    public function test_if_can_save_the_genre_on_database_correctly(): void
    {
        $this->putJson(route('genres.update', $this->genre), $data = $this->getValidPayload())->assertOk();

        $this->assertDatabaseHas('genres', [
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
        $this->putJson(route('genres.update', $this->genre), $this->getValidPayload())->assertOk()->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'slug',
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
        $this->putJson(route('genres.update', $this->genre), $data = $this->getValidPayload())->assertOk()->assertJson([
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
