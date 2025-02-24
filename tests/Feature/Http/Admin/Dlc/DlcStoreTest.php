<?php

namespace Tests\Feature\Http\Admin\Dlc;

use Mockery;
use Exception;
use App\Models\User;
use Illuminate\Support\Carbon;
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyDlc,
    HasDummyGame,
    HasDummyPermission,
};
use App\Contracts\Services\{
    LogServiceInterface,
    DlcServiceInterface,
};

class DlcStoreTest extends BaseIntegrationTesting
{
    use HasDummyDlc;
    use HasDummyGame;
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
        'view:dlcs',
        'create:dlcs',
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
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'title' => fake()->title(),
            'short_description' => fake()->text(),
            'game_id' => $this->createDummyGame()->id,
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

        $this->postJson(route('dlcs.store'), $this->getValidPayload())
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

        $this->postJson(route('dlcs.store'), $this->getValidPayload())->assertNotFound();
    }

    /**
     * Test if can't create a Dlc without payload.
     *
     * @return void
     */
    public function test_if_cant_create_a_Dlc_without_payload(): void
    {
        $this->postJson(route('dlcs.store'))->assertUnprocessable();
    }

    /**
     * Test if can throw correct invalid json keys.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_keys(): void
    {
        $this->postJson(route('dlcs.store'))
            ->assertUnprocessable()
            ->assertInvalid([
                'free',
                'about',
                'cover',
                'title',
                'game_id',
                'release_date',
                'short_description',
            ]);
    }

    /**
     * Test if can throw correct invalid json messages.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_messages(): void
    {
        $this->postJson(route('dlcs.store'))
            ->assertUnprocessable()
            ->assertInvalid([
                'free',
                'about',
                'cover',
                'title',
                'game_id',
                'release_date',
                'short_description',
            ])->assertSee('The free field is required. (and 6 more errors)');
    }

    /**
     * Test if can't create a duplicated Dlc.
     *
     * @return void
     */
    public function test_if_cant_create_a_duplicated_dlc(): void
    {
        $dlc = $this->createDummyDlc();

        $data = [
            ...$this->getValidPayload(),
            'title' => $dlc->title,
        ];

        $this->postJson(route('dlcs.store'), $data)
            ->assertUnprocessable()
            ->assertInvalid(['title'])
            ->assertSee('The title has already been taken.');
    }

    /**
     * Test if can log context on dlc creation failure.
     *
     * @return void
     */
    public function test_if_can_log_context_on_dlc_creation_failure(): void
    {
        $logServiceMock = Mockery::mock(LogServiceInterface::class);
        $logServiceMock->shouldReceive('withContext')
            ->once()
            ->with(
                Mockery::on(function (string $title) {
                    return $title === 'Failed to create a new dlc.';
                }),
                Mockery::on(function (array $context) {
                    return isset($context['code'], $context['message'], $context['trace']);
                }),
            );

        $this->app->instance(LogServiceInterface::class, $logServiceMock);

        $exception = new Exception('Test exception', 500);
        $dlcServiceMock = Mockery::mock(DlcServiceInterface::class);
        $dlcServiceMock->shouldReceive('create')
            ->once()
            ->andThrow($exception);

        $this->app->instance(DlcServiceInterface::class, $dlcServiceMock);

        $this->postJson(route('dlcs.store'), $this->getValidPayload())
            ->assertServerError()
            ->assertSee('Test exception');
    }

    /**
     * Test if can create a dlc with valid payload.
     *
     * @return void
     */
    public function test_if_can_create_a_dlc_with_valid_payload(): void
    {
        $this->postJson(route('dlcs.store'), $this->getValidPayload())->assertCreated();
    }

    /**
     * Test if can save the dlc on database correctly.
     *
     * @return void
     */
    public function test_if_can_save_the_dlc_on_database_correctly(): void
    {
        $this->postJson(route('dlcs.store'), $data = $this->getValidPayload())->assertCreated();

        /** @var string $releaseDate */
        $releaseDate = $data['release_date'];

        $this->assertDatabaseHas('dlcs', [
            'free' => $data['free'],
            'legal' => $data['legal'],
            'about' => clean($data['about']),
            'cover' => $data['cover'],
            'release_date' => Carbon::parse($releaseDate)->toDateTimeString(),
            'description' => clean($data['description']),
            'title' => $data['title'],
            'short_description' => $data['short_description'],
            'game_id' => $data['game_id'],
        ]);
    }

    /**
     * Test if can get correct json structure response.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_structure_response(): void
    {
        $this->postJson(route('dlcs.store'), $this->getValidPayload())->assertCreated()->assertJsonStructure([
            'data' => [
                'id',
                'slug',
                'free',
                'title',
                'cover',
                'legal',
                'about',
                'description',
                'release_date',
                'short_description',
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
        $data = $this->getValidPayload();

        /** @var string $releaseDate */
        $releaseDate = $data['release_date'];

        $this->postJson(route('dlcs.store'), $data)->assertCreated()->assertJson([
            'data' => [
                'free' => $data['free'],
                'legal' => $data['legal'],
                'about' => clean($data['about']),
                'cover' => $data['cover'],
                'release_date' => Carbon::parse($releaseDate)->toISOString(),
                'description' => clean($data['description']),
                'title' => $data['title'],
                'short_description' => $data['short_description'],
            ],
        ]);
    }

    /**
     * Test if can sanitize about and description fields.
     *
     * @return void
     */
    public function test_if_can_sanitize_about_and_description_fields(): void
    {
        $data = [
            ...$this->getValidPayload(),
            'about' => '<script>alert("hacked!")</script><p style="color:#eb4034;">P field</p>',
            'description' => '<p style="color:#1e1bc2;">P field</p><script>alert("hacked!")</script>',
        ];

        $this->postJson(route('dlcs.store'), $data)->assertCreated();

        /** @var string $releaseDate */
        $releaseDate = $data['release_date'];

        $this->assertDatabaseHas('dlcs', [
            'free' => $data['free'],
            'legal' => $data['legal'],
            'about' => '<p style="color:#eb4034;">P field</p>',
            'cover' => $data['cover'],
            'release_date' => Carbon::parse($releaseDate)->toDateTimeString(),
            'description' => '<p style="color:#1e1bc2;">P field</p>',
            'title' => $data['title'],
            'short_description' => $data['short_description'],
            'game_id' => $data['game_id'],
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
