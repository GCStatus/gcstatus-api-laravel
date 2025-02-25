<?php

namespace Tests\Feature\Http\Admin\RequirementType;

use Mockery;
use Exception;
use App\Models\{User, RequirementType};
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyPermission,
    HasDummyRequirementType,
};
use App\Contracts\Services\{
    LogServiceInterface,
    RequirementTypeServiceInterface,
};

class RequirementTypeStoreTest extends BaseIntegrationTesting
{
    use HasDummyPermission;
    use HasDummyRequirementType;

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
        'view:requirement-types',
        'create:requirement-types',
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

        RequirementType::all()->each(fn (RequirementType $t) => $t->delete());
    }

    /**
     * Get a valid payload.
     *
     * @return array<string, mixed>
     */
    private function getValidPayload(): array
    {
        return [
            'os' => fake()->randomElement(['windows', 'linux', 'mac']),
            'potential' => fake()->randomElement(['minimum', 'recommended', 'maximum']),
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

        $this->postJson(route('requirement-types.store'), $this->getValidPayload())
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

        $this->postJson(route('requirement-types.store'), $this->getValidPayload())->assertNotFound();
    }

    /**
     * Test if can't create a RequirementType without payload.
     *
     * @return void
     */
    public function test_if_cant_create_a_requirement_type_without_payload(): void
    {
        $this->postJson(route('requirement-types.store'))->assertUnprocessable();
    }

    /**
     * Test if can throw correct invalid json keys.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_keys(): void
    {
        $this->postJson(route('requirement-types.store'))
            ->assertUnprocessable()
            ->assertInvalid(['os', 'potential']);
    }

    /**
     * Test if can throw correct invalid json messages.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_messages(): void
    {
        $this->postJson(route('requirement-types.store'))
            ->assertUnprocessable()
            ->assertInvalid(['os', 'potential'])
            ->assertSee('The os field is required. (and 1 more error)');
    }

    /**
     * Test if can create a duplicated os on database.
     *
     * @return void
     */
    public function test_if_can_create_a_duplicated_os_on_database(): void
    {
        $requirementType = $this->createDummyRequirementType();

        $data = [
            'os' => $requirementType->os,
            'potential' => fake()->randomElement(
                array_diff(['minimum', 'recommended', 'maximum'], [$requirementType->potential])
            ),
        ];

        $this->postJson(route('requirement-types.store'), $data)->assertCreated();
    }

    /**
     * Test if can create a duplicated potential on database.
     *
     * @return void
     */
    public function test_if_can_create_a_duplicated_potential_on_database(): void
    {
        $requirementType = $this->createDummyRequirementType();

        $data = [
            'os' => fake()->randomElement(
                array_diff(['windows', 'linux', 'mac'], [$requirementType->os])
            ),
            'potential' => $requirementType->potential,
        ];

        $this->postJson(route('requirement-types.store'), $data)->assertCreated();
    }

    /**
     * Test if can log context on requirement type creation failure.
     *
     * @return void
     */
    public function test_if_can_log_context_on_requirement_type_creation_failure(): void
    {
        $logServiceMock = Mockery::mock(LogServiceInterface::class);
        $logServiceMock->shouldReceive('withContext')
            ->once()
            ->with(
                Mockery::on(function (string $title) {
                    return $title === 'Failed to create a new requirement type.';
                }),
                Mockery::on(function (array $context) {
                    return isset($context['code'], $context['message'], $context['trace']);
                }),
            );

        $this->app->instance(LogServiceInterface::class, $logServiceMock);

        $exception = new Exception('Test exception', 500);
        $requirementTypeServiceMock = Mockery::mock(RequirementTypeServiceInterface::class);
        $requirementTypeServiceMock->shouldReceive('create')
            ->once()
            ->andThrow($exception);

        $this->app->instance(RequirementTypeServiceInterface::class, $requirementTypeServiceMock);

        $this->postJson(route('requirement-types.store'), $this->getValidPayload())
            ->assertServerError()
            ->assertSee('Test exception');
    }

    /**
     * Test if can create a requirement type with valid payload.
     *
     * @return void
     */
    public function test_if_can_create_a_requirement_type_with_valid_payload(): void
    {
        $this->postJson(route('requirement-types.store'), $this->getValidPayload())->assertCreated();
    }

    /**
     * Test if can save the requirement type on database correctly.
     *
     * @return void
     */
    public function test_if_can_save_the_requirement_type_on_database_correctly(): void
    {
        $this->postJson(route('requirement-types.store'), $data = $this->getValidPayload())->assertCreated();

        $this->assertDatabaseHas('requirement_types', [
            'os' => $data['os'],
            'potential' => $data['potential'],
        ]);
    }

    /**
     * Test if can get correct json structure response.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_structure_response(): void
    {
        $this->postJson(route('requirement-types.store'), $this->getValidPayload())->assertCreated()->assertJsonStructure([
            'data' => [
                'id',
                'os',
                'potential',
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
        $this->postJson(route('requirement-types.store'), $data = $this->getValidPayload())->assertCreated()->assertJson([
            'data' => [
                'os' => $data['os'],
                'potential' => $data['potential'],
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
