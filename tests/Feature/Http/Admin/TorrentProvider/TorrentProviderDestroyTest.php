<?php

namespace Tests\Feature\Http\Admin\TorrentProvider;

use App\Models\{TorrentProvider, User};
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyPermission,
    HasDummyTorrentProvider,
};

class TorrentProviderDestroyTest extends BaseIntegrationTesting
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
        'delete:torrent-providers',
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
     * Test if can't see if is unauthenticated.
     *
     * @return void
     */
    public function test_if_cant_see_if_user_is_unauthenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->deleteJson(route('torrent-providers.destroy', $this->torrentProvider))
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

        $this->deleteJson(route('torrent-providers.destroy', $this->torrentProvider))->assertNotFound();
    }

    /**
     * Test if can soft delete a torrent provider.
     *
     * @return void
     */
    public function test_if_can_soft_delete_a_torrent_provider(): void
    {
        $this->assertNotSoftDeleted($this->torrentProvider);

        $this->deleteJson(route('torrent-providers.destroy', $this->torrentProvider))->assertOk();

        $this->assertSoftDeleted($this->torrentProvider);
    }
}
