<?php

namespace Tests\Feature\Http\Admin\RequirementType;

use Mockery;
use Exception;
use App\Models\{RequirementType, User};
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyPermission,
    HasDummyRequirementType,
};
use App\Contracts\Services\{
    LogServiceInterface,
    RequirementTypeServiceInterface,
};

class RequirementTypeUpdateTest extends BaseIntegrationTesting
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
     * The dummy requirement yype.
     *
     * @var \App\Models\RequirementType
     */
    private RequirementType $requirementType;

    /**
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'view:requirement-types',
        'update:requirement-types',
    ];

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        RequirementType::all()->each(fn (RequirementType $t) => $t->delete());

        $this->requirementType = $this->createDummyRequirementType();

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

        $this->putJson(route('requirement-types.update', $this->requirementType), $this->getValidPayload())
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

        $this->putJson(route('requirement-types.update', $this->requirementType), $this->getValidPayload())->assertNotFound();
    }

    /**
     * Test if can't update a RequirementType without payload.
     *
     * @return void
     */
    public function test_if_cant_update_a_requirement_type_without_payload(): void
    {
        $this->putJson(route('requirement-types.update', $this->requirementType))->assertUnprocessable();
    }

    /**
     * Test if can throw correct invalid json keys.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_keys(): void
    {
        $this->putJson(route('requirement-types.update', $this->requirementType))
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
        $this->putJson(route('requirement-types.update', $this->requirementType))
            ->assertUnprocessable()
            ->assertInvalid(['os', 'potential'])
            ->assertSee('The os field is required. (and 1 more error)');
    }

    /**
     * Test if can update to a duplicated os on database.
     *
     * @return void
     */
    public function test_if_can_update_to_a_duplicated_os_on_database(): void
    {
        $requirementType = $this->createDummyRequirementType();

        $data = [
            'os' => $requirementType->os,
            'potential' => fake()->randomElement(
                array_diff(['minimum', 'recommended', 'maximum'], [$requirementType->potential])
            ),
        ];

        $this->putJson(route('requirement-types.update', $this->requirementType), $data)->assertOk();
    }

    /**
     * Test if can update to a duplicated potential on database.
     *
     * @return void
     */
    public function test_if_can_update_to_a_duplicated_potential_on_database(): void
    {
        $requirementType = $this->createDummyRequirementType();

        $data = [
            'os' => fake()->randomElement(
                array_diff(['windows', 'linux', 'mac'], [$requirementType->os])
            ),
            'potential' => $requirementType->potential,
        ];

        $this->putJson(route('requirement-types.update', $this->requirementType), $data)->assertOk();
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
                    return $title === 'Failed to update a requirement type.';
                }),
                Mockery::on(function (array $context) {
                    return isset($context['code'], $context['message'], $context['trace']);
                }),
            );

        $this->app->instance(LogServiceInterface::class, $logServiceMock);

        $exception = new Exception('Test exception', 500);
        $requirementTypeServiceMock = Mockery::mock(RequirementTypeServiceInterface::class);
        $requirementTypeServiceMock->shouldReceive('update')
            ->once()
            ->andThrow($exception);

        $this->app->instance(RequirementTypeServiceInterface::class, $requirementTypeServiceMock);

        $this->putJson(route('requirement-types.update', $this->requirementType), $this->getValidPayload())
            ->assertServerError()
            ->assertSee('Test exception');
    }

    /**
     * Test if can ignore self "duplicated" requirement type to update.
     *
     * @return void
     */
    public function test_if_can_ignore_self_duplicated_requirement_type_to_update(): void
    {
        $this->putJson(route('requirement-types.update', $this->requirementType), [
            'os' => $this->requirementType->os,
            'potential' => $this->requirementType->potential,
        ])->assertOk();
    }

    /**
     * Test if can update a requirement type with valid payload.
     *
     * @return void
     */
    public function test_if_can_update_a_requirement_type_with_valid_payload(): void
    {
        $this->putJson(route('requirement-types.update', $this->requirementType), $this->getValidPayload())->assertOk();
    }

    /**
     * Test if can save the requirement type on database correctly.
     *
     * @return void
     */
    public function test_if_can_save_the_requirement_type_on_database_correctly(): void
    {
        $this->putJson(route('requirement-types.update', $this->requirementType), $data = $this->getValidPayload())->assertOk();

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
        $this->putJson(route('requirement-types.update', $this->requirementType), $this->getValidPayload())->assertOk()->assertJsonStructure([
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
        $this->putJson(route('requirement-types.update', $this->requirementType), $data = $this->getValidPayload())->assertOk()->assertJson([
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
