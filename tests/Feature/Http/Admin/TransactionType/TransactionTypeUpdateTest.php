<?php

namespace Tests\Feature\Http\Admin\TransactionType;

use Mockery;
use Exception;
use App\Models\{TransactionType, User};
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyPermission,
    HasDummyTransactionType,
};
use App\Contracts\Services\{
    LogServiceInterface,
    TransactionTypeServiceInterface,
};

class TransactionTypeUpdateTest extends BaseIntegrationTesting
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
        'update:transaction-types',
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
     * Get a valid payload.
     *
     * @return array<string, string>
     */
    private function getValidPayload(): array
    {
        return [
            'type' => fake()->word(),
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

        $this->putJson(route('transaction-types.update', $this->transactionType), $this->getValidPayload())
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

        $this->putJson(route('transaction-types.update', $this->transactionType), $this->getValidPayload())->assertNotFound();
    }

    /**
     * Test if can't update a transactionType without payload.
     *
     * @return void
     */
    public function test_if_cant_update_a_transactionType_without_payload(): void
    {
        $this->putJson(route('transaction-types.update', $this->transactionType))->assertUnprocessable();
    }

    /**
     * Test if can throw correct invalid json keys.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_keys(): void
    {
        $this->putJson(route('transaction-types.update', $this->transactionType))
            ->assertUnprocessable()
            ->assertInvalid(['type']);
    }

    /**
     * Test if can throw correct invalid json messages.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_messages(): void
    {
        $this->putJson(route('transaction-types.update', $this->transactionType))
            ->assertUnprocessable()
            ->assertInvalid(['type'])
            ->assertSee('The type field is required.');
    }

    /**
     * Test if can't update the name to a duplicated transactionType.
     *
     * @return void
     */
    public function test_if_cant_update_name_to_a_duplicated_transactionType(): void
    {
        $transactionType = $this->createDummyTransactionType();

        $data = [
            'type' => $transactionType->type,
        ];

        $this->putJson(route('transaction-types.update', $this->transactionType), $data)
            ->assertUnprocessable()
            ->assertInvalid(['type'])
            ->assertSee('The type has already been taken.');
    }

    /**
     * Test if can log context on transactionType creation failure.
     *
     * @return void
     */
    public function test_if_can_log_context_on_transactionType_creation_failure(): void
    {
        $logServiceMock = Mockery::mock(LogServiceInterface::class);
        $logServiceMock->shouldReceive('withContext')
            ->once()
            ->with(
                Mockery::on(function (string $title) {
                    return $title === 'Failed to update a transaction type.';
                }),
                Mockery::on(function (array $context) {
                    return isset($context['code'], $context['message'], $context['trace']);
                }),
            );

        $this->app->instance(LogServiceInterface::class, $logServiceMock);

        $exception = new Exception('Test exception', 500);
        $transactionTypeServiceMock = Mockery::mock(TransactionTypeServiceInterface::class);
        $transactionTypeServiceMock->shouldReceive('update')
            ->once()
            ->andThrow($exception);

        $this->app->instance(TransactionTypeServiceInterface::class, $transactionTypeServiceMock);

        $this->putJson(route('transaction-types.update', $this->transactionType), $this->getValidPayload())
            ->assertServerError()
            ->assertSee('Test exception');
    }

    /**
     * Test if can ignore self "duplicated" transactionType to update.
     *
     * @return void
     */
    public function test_if_can_ignore_self_duplicated_transactionType_to_update(): void
    {
        $this->putJson(route('transaction-types.update', $this->transactionType), [
            'type' => $this->transactionType->type,
        ])->assertOk();
    }

    /**
     * Test if can create a transactionType with valid payload.
     *
     * @return void
     */
    public function test_if_can_create_a_transactionType_with_valid_payload(): void
    {
        $this->putJson(route('transaction-types.update', $this->transactionType), $this->getValidPayload())->assertOk();
    }

    /**
     * Test if can save the transactionType on database correctly.
     *
     * @return void
     */
    public function test_if_can_save_the_transactionType_on_database_correctly(): void
    {
        $this->putJson(route('transaction-types.update', $this->transactionType), $data = $this->getValidPayload())->assertOk();

        $this->assertDatabaseHas('transaction_types', [
            'type' => $data['type'],
        ]);
    }

    /**
     * Test if can get correct json structure response.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_structure_response(): void
    {
        $this->putJson(route('transaction-types.update', $this->transactionType), $this->getValidPayload())->assertOk()->assertJsonStructure([
            'data' => [
                'id',
                'type',
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
        $this->putJson(route('transaction-types.update', $this->transactionType), $data = $this->getValidPayload())->assertOk()->assertJson([
            'data' => [
                'type' => $data['type'],
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
