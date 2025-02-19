<?php

namespace Tests\Feature\Http\Admin\category;

use Mockery;
use Exception;
use App\Models\User;
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyCategory,
    HasDummyPermission,
};
use App\Contracts\Services\{
    LogServiceInterface,
    CategoryServiceInterface,
};

class CategoryStoreTest extends BaseIntegrationTesting
{
    use HasDummyCategory;
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
        'view:categories',
        'create:categories',
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
            'name' => fake()->word(),
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

        $this->postJson(route('categories.store'), $this->getValidPayload())
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

        $this->postJson(route('categories.store'), $this->getValidPayload())->assertNotFound();
    }

    /**
     * Test if can't create a category without payload.
     *
     * @return void
     */
    public function test_if_cant_create_a_category_without_payload(): void
    {
        $this->postJson(route('categories.store'))->assertUnprocessable();
    }

    /**
     * Test if can throw correct invalid json keys.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_keys(): void
    {
        $this->postJson(route('categories.store'))
            ->assertUnprocessable()
            ->assertInvalid(['name']);
    }

    /**
     * Test if can throw correct invalid json messages.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_messages(): void
    {
        $this->postJson(route('categories.store'))
            ->assertUnprocessable()
            ->assertInvalid(['name'])
            ->assertSee('The name field is required.');
    }

    /**
     * Test if can't create a duplicated category.
     *
     * @return void
     */
    public function test_if_cant_create_a_duplicated_category(): void
    {
        $category = $this->createDummyCategory();

        $data = [
            'name' => $category->name,
        ];

        $this->postJson(route('categories.store'), $data)
            ->assertUnprocessable()
            ->assertInvalid(['name'])
            ->assertSee('The name has already been taken.');
    }

    /**
     * Test if can log context on category creation failure.
     *
     * @return void
     */
    public function test_if_can_log_context_on_category_creation_failure(): void
    {
        $logServiceMock = Mockery::mock(LogServiceInterface::class);
        $logServiceMock->shouldReceive('withContext')
            ->once()
            ->with(
                Mockery::on(function (string $title) {
                    return $title === 'Failed to create a new category.';
                }),
                Mockery::on(function (array $context) {
                    return isset($context['code'], $context['message'], $context['trace']);
                }),
            );

        $this->app->instance(LogServiceInterface::class, $logServiceMock);

        $exception = new Exception('Test exception', 500);
        $categorieserviceMock = Mockery::mock(CategoryServiceInterface::class);
        $categorieserviceMock->shouldReceive('create')
            ->once()
            ->andThrow($exception);

        $this->app->instance(CategoryServiceInterface::class, $categorieserviceMock);

        $this->postJson(route('categories.store'), $this->getValidPayload())
            ->assertServerError()
            ->assertSee('Test exception');
    }

    /**
     * Test if can create a category with valid payload.
     *
     * @return void
     */
    public function test_if_can_create_a_category_with_valid_payload(): void
    {
        $this->postJson(route('categories.store'), $this->getValidPayload())->assertCreated();
    }

    /**
     * Test if can save the category on database correctly.
     *
     * @return void
     */
    public function test_if_can_save_the_category_on_database_correctly(): void
    {
        $this->postJson(route('categories.store'), $data = $this->getValidPayload())->assertCreated();

        $this->assertDatabaseHas('categories', [
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
        $this->postJson(route('categories.store'), $this->getValidPayload())->assertCreated()->assertJsonStructure([
            'data' => [
                'id',
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
        $this->postJson(route('categories.store'), $data = $this->getValidPayload())->assertCreated()->assertJson([
            'data' => [
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
