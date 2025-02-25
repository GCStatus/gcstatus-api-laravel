<?php

namespace Tests\Feature\Http\Admin\TransactionType;

use App\Models\{TransactionType, User};
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyPermission,
    HasDummyTransactionType,
};

class TransactionTypeDestroyTest extends BaseIntegrationTesting
{
    use HasDummyTransactionType;
    use HasDummyPermission;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The dummy transactionType.
     *
     * @var \App\Models\TransactionType
     */
    private TransactionType $transactionType;

    /**
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'view:transaction-types',
        'delete:transaction-types',
    ];

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->transactionType = $this->createDummyTransactionType();

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

        $this->deleteJson(route('transaction-types.destroy', $this->transactionType))
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

        $this->deleteJson(route('transaction-types.destroy', $this->transactionType))->assertNotFound();
    }

    /**
     * Test if can soft delete a torrent provider.
     *
     * @return void
     */
    public function test_if_can_soft_delete_a_torrent_provider(): void
    {
        $this->assertNotSoftDeleted($this->transactionType);

        $this->deleteJson(route('transaction-types.destroy', $this->transactionType))->assertOk();

        $this->assertSoftDeleted($this->transactionType);
    }
}
