<?php

namespace Tests\Feature\Http;

use Tests\TestCase;
use App\Models\{User, Role};
use App\Contracts\Services\AuthServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\{
    HasDummyUser,
    HasDummyPermission,
};
use Database\Seeders\{
    LevelSeeder,
    StatusSeeder,
    MediaTypeSeeder,
    RequirementTypeSeeder,
    TransactionTypeSeeder,
};

abstract class BaseIntegrationTesting extends TestCase
{
    use HasDummyUser;
    use RefreshDatabase;
    use HasDummyPermission;

    /**
     * The auth service.
     *
     * @var \App\Contracts\Services\AuthServiceInterface
     */
    protected AuthServiceInterface $authService;

    /**
     * The permissions to act a test endpoint.
     *
     * @var list<string>
     */
    protected array $permissions;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->seed([
            LevelSeeder::class,
            StatusSeeder::class,
            TransactionTypeSeeder::class,
            MediaTypeSeeder::class,
            RequirementTypeSeeder::class,
        ]);

        $this->authService = app(AuthServiceInterface::class);
    }

    /**
     * Boot the user permissions.
     *
     * @param \App\Models\User $user
     * @return void
     */
    protected function bootUserPermissions(User $user): void
    {
        foreach ($this->permissions as $permission) {
            $user->permissions()->save(
                $this->createDummyPermission([
                    'scope' => $permission,
                ]),
            );
        }
    }

    /**
     * Boot the role permissions.
     *
     * @param \App\Models\Role $role
     * @return void
     */
    protected function bootRolePermissions(Role $role): void
    {
        foreach ($this->permissions as $permission) {
            $role->permissions()->save(
                $this->createDummyPermission([
                    'scope' => $permission,
                ]),
            );
        }
    }
}
