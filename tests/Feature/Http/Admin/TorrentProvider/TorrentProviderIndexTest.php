<?php

namespace Tests\Feature\Http\Admin\TorrentProvider;

use App\Models\{TorrentProvider, User};
use Illuminate\Database\Eloquent\Collection;
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyPermission,
    HasDummyTorrentProvider,
};

class TorrentProviderIndexTest extends BaseIntegrationTesting
{
    use HasDummyPermission;
    use HasDummyTorrentProvider;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The dummy torrentProviders.
     *
     * @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\TorrentProvider>
     */
    private Collection $torrentProviders;

    /**
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'view:torrent-providers',
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

        $this->torrentProviders = $this->createDummyTorrentProviders(4);
    }

    /**
     * Test if can't see if is unauthenticated.
     *
     * @return void
     */
    public function test_if_cant_see_if_user_is_unauthenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->getJson(route('torrent-providers.index'))
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

        $this->getJson(route('torrent-providers.index'))->assertNotFound();
    }

    /**
     * Test if can see torrentProviders if has permissions.
     *
     * @return void
     */
    public function test_if_can_see_torrentProviders_if_has_permissions(): void
    {
        $this->getJson(route('torrent-providers.index'))->assertOk();
    }

    /**
     * Test if can get correct json attributes count.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_attributes_count(): void
    {
        $this->getJson(route('torrent-providers.index'))->assertOk()->assertJsonCount(4, 'data');
    }

    /**
     * Test if can get correct json structure.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_structure(): void
    {
        $this->getJson(route('torrent-providers.index'))->assertOk()->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'url',
                    'name',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }

    /**
     * Test if can get correct json data.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_data(): void
    {
        $this->getJson(route('torrent-providers.index'))->assertOk()->assertJson([
            'data' => $this->torrentProviders->map(function (TorrentProvider $torrentProvider) {
                return [
                    'id' => $torrentProvider->id,
                    'url' => $torrentProvider->url,
                    'name' => $torrentProvider->name,
                    'created_at' => $torrentProvider->created_at?->toISOString(),
                    'updated_at' => $torrentProvider->updated_at?->toISOString(),
                ];
            })->toArray(),
        ]);
    }
}
