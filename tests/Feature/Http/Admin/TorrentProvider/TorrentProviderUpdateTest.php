<?php

namespace Tests\Feature\Http\Admin\TorrentProvider;

use Mockery;
use Exception;
use App\Models\{TorrentProvider, User};
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyPermission,
    HasDummyTorrentProvider,
};
use App\Contracts\Services\{
    LogServiceInterface,
    TorrentProviderServiceInterface,
};

class TorrentProviderUpdateTest extends BaseIntegrationTesting
{
    use HasDummyTorrentProvider;
    use HasDummyPermission;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The dummy torrentProvider.
     *
     * @var \App\Models\TorrentProvider
     */
    private TorrentProvider $torrentProvider;

    /**
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'view:torrent-providers',
        'update:torrent-providers',
    ];

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->torrentProvider = $this->createDummyTorrentProvider();

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

        $this->putJson(route('torrent-providers.update', $this->torrentProvider), $this->getValidPayload())
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

        $this->putJson(route('torrent-providers.update', $this->torrentProvider), $this->getValidPayload())->assertNotFound();
    }

    /**
     * Test if can update a torrentProvider without payload.
     *
     * @return void
     */
    public function test_if_can_update_a_torrentProvider_without_payload(): void
    {
        $this->putJson(route('torrent-providers.update', $this->torrentProvider))->assertOk();
    }

    /**
     * Test if can't update the name to a duplicated torrentProvider.
     *
     * @return void
     */
    public function test_if_cant_update_name_to_a_duplicated_torrentProvider(): void
    {
        $torrentProvider = $this->createDummyTorrentProvider();

        $data = [
            ...$this->getValidPayload(),
            'name' => $torrentProvider->name,
        ];

        $this->putJson(route('torrent-providers.update', $this->torrentProvider), $data)
            ->assertUnprocessable()
            ->assertInvalid(['name'])
            ->assertSee('The name has already been taken.');
    }

    /**
     * Test if can log context on torrentProvider creation failure.
     *
     * @return void
     */
    public function test_if_can_log_context_on_torrentProvider_creation_failure(): void
    {
        $logServiceMock = Mockery::mock(LogServiceInterface::class);
        $logServiceMock->shouldReceive('withContext')
            ->once()
            ->with(
                Mockery::on(function (string $title) {
                    return $title === 'Failed to update a torrent provider.';
                }),
                Mockery::on(function (array $context) {
                    return isset($context['code'], $context['message'], $context['trace']);
                }),
            );

        $this->app->instance(LogServiceInterface::class, $logServiceMock);

        $exception = new Exception('Test exception', 500);
        $torrentProviderServiceMock = Mockery::mock(TorrentProviderServiceInterface::class);
        $torrentProviderServiceMock->shouldReceive('update')
            ->once()
            ->andThrow($exception);

        $this->app->instance(TorrentProviderServiceInterface::class, $torrentProviderServiceMock);

        $this->putJson(route('torrent-providers.update', $this->torrentProvider), $this->getValidPayload())
            ->assertServerError()
            ->assertSee('Test exception');
    }

    /**
     * Test if can ignore self "duplicated" torrentProvider to update.
     *
     * @return void
     */
    public function test_if_can_ignore_self_duplicated_torrentProvider_to_update(): void
    {
        $this->putJson(route('torrent-providers.update', $this->torrentProvider), [
            'name' => $this->torrentProvider->name,
        ])->assertOk();
    }

    /**
     * Test if can create a torrentProvider with valid payload.
     *
     * @return void
     */
    public function test_if_can_create_a_torrentProvider_with_valid_payload(): void
    {
        $this->putJson(route('torrent-providers.update', $this->torrentProvider), $this->getValidPayload())->assertOk();
    }

    /**
     * Test if can save the torrentProvider on database correctly.
     *
     * @return void
     */
    public function test_if_can_save_the_torrentProvider_on_database_correctly(): void
    {
        $this->putJson(route('torrent-providers.update', $this->torrentProvider), $data = $this->getValidPayload())->assertOk();

        $this->assertDatabaseHas('torrent_providers', [
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
        $this->putJson(route('torrent-providers.update', $this->torrentProvider), $this->getValidPayload())->assertOk()->assertJsonStructure([
            'data' => [
                'id',
                'url',
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
        $this->putJson(route('torrent-providers.update', $this->torrentProvider), $data = $this->getValidPayload())->assertOk()->assertJson([
            'data' => [
                'url' => $data['url'],
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
