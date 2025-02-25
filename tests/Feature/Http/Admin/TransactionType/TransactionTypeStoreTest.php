<?php

namespace Tests\Feature\Http\Admin\TransactionType;

use Mockery;
use Exception;
use App\Models\User;
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyPermission,
    HasDummyTransactionType,
};
use App\Contracts\Services\{
    LogServiceInterface,
    TransactionTypeServiceInterface,
};

class TransactionTypeStoreTest extends BaseIntegrationTesting
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
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'view:transaction-types',
        'create:transaction-types',
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

        $this->postJson(route('transaction-types.store'), $this->getValidPayload())
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

        $this->postJson(route('transaction-types.store'), $this->getValidPayload())->assertNotFound();
    }

    /**
     * Test if can't create a transactionType without payload.
     *
     * @return void
     */
    public function test_if_cant_create_a_transactionType_without_payload(): void
    {
        $this->postJson(route('transaction-types.store'))->assertUnprocessable();
    }

    /**
     * Test if can throw correct invalid json keys.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_keys(): void
    {
        $this->postJson(route('transaction-types.store'))
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
        $this->postJson(route('transaction-types.store'))
            ->assertUnprocessable()
            ->assertInvalid(['type'])
            ->assertSee('The type field is required.');
    }

    /**
     * Test if can't create a duplicated transactionType.
     *
     * @return void
     */
    public function test_if_cant_create_a_duplicated_transactionType(): void
    {
        $transactionType = $this->createDummyTransactionType();

        $data = [
            'type' => $transactionType->type,
        ];

        $this->postJson(route('transaction-types.store'), $data)
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
                    return $title === 'Failed to create a new transaction type.';
                }),
                Mockery::on(function (array $context) {
                    return isset($context['code'], $context['message'], $context['trace']);
                }),
            );

        $this->app->instance(LogServiceInterface::class, $logServiceMock);

        $exception = new Exception('Test exception', 500);
        $transactionTypeServiceMock = Mockery::mock(TransactionTypeServiceInterface::class);
        $transactionTypeServiceMock->shouldReceive('create')
            ->once()
            ->andThrow($exception);

        $this->app->instance(TransactionTypeServiceInterface::class, $transactionTypeServiceMock);

        $this->postJson(route('transaction-types.store'), $this->getValidPayload())
            ->assertServerError()
            ->assertSee('Test exception');
    }

    /**
     * Test if can create a transactionType with valid payload.
     *
     * @return void
     */
    public function test_if_can_create_a_transactionType_with_valid_payload(): void
    {
        $this->postJson(route('transaction-types.store'), $this->getValidPayload())->assertCreated();
    }

    /**
     * Test if can save the transactionType on database correctly.
     *
     * @return void
     */
    public function test_if_can_save_the_transactionType_on_database_correctly(): void
    {
        $this->postJson(route('transaction-types.store'), $data = $this->getValidPayload())->assertCreated();

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
        $this->postJson(route('transaction-types.store'), $this->getValidPayload())->assertCreated()->assertJsonStructure([
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
        $this->postJson(route('transaction-types.store'), $data = $this->getValidPayload())->assertCreated()->assertJson([
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
