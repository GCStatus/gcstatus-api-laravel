<?php

namespace Tests\Feature\Http\Admin\TransactionType;

use App\Models\{TransactionType, User};
use Illuminate\Database\Eloquent\Collection;
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyPermission,
    HasDummyTransactionType,
};

class TransactionTypeIndexTest extends BaseIntegrationTesting
{
    use HasDummyPermission;
    use HasDummyTransactionType;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The dummy transactionTypes.
     *
     * @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\TransactionType>
     */
    private Collection $transactionTypes;

    /**
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'view:transaction-types',
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

        TransactionType::all()->each(fn (TransactionType $t) => $t->delete());

        $this->transactionTypes = $this->createDummyTransactionTypes(4);
    }

    /**
     * Test if can't see if is unauthenticated.
     *
     * @return void
     */
    public function test_if_cant_see_if_user_is_unauthenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->getJson(route('transaction-types.index'))
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

        $this->getJson(route('transaction-types.index'))->assertNotFound();
    }

    /**
     * Test if can see transactionTypes if has permissions.
     *
     * @return void
     */
    public function test_if_can_see_transactionTypes_if_has_permissions(): void
    {
        $this->getJson(route('transaction-types.index'))->assertOk();
    }

    /**
     * Test if can get correct json attributes count.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_attributes_count(): void
    {
        $this->getJson(route('transaction-types.index'))->assertOk()->assertJsonCount(4, 'data');
    }

    /**
     * Test if can get correct json structure.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_structure(): void
    {
        $this->getJson(route('transaction-types.index'))->assertOk()->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'type',
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
        $this->getJson(route('transaction-types.index'))->assertOk()->assertJson([
            'data' => $this->transactionTypes->map(function (TransactionType $transactionType) {
                return [
                    'id' => $transactionType->id,
                    'type' => $transactionType->type,
                    'created_at' => $transactionType->created_at?->toISOString(),
                    'updated_at' => $transactionType->updated_at?->toISOString(),
                ];
            })->toArray(),
        ]);
    }
}
