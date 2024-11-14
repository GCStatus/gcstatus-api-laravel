<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\{User, Level};
use Illuminate\Support\Carbon;
use Tests\Traits\HasDummyUser;
use Database\Seeders\LevelSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Contracts\Services\AbstractServiceInterface;
use Illuminate\Database\Eloquent\{Collection, ModelNotFoundException};
use Tests\Implementations\{
    TestAbstractServiceInterface,
    TestAbstractRepositoryInterface,
};

class AbstractServiceInterfaceTest extends TestCase
{
    use HasDummyUser;
    use RefreshDatabase;

    /**
     * The abstract service.
     *
     * @var \App\Contracts\Services\AbstractServiceInterface
     */
    protected AbstractServiceInterface $service;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([LevelSeeder::class]);

        $this->service = new TestAbstractServiceInterface(
            new TestAbstractRepositoryInterface(
                new User(),
            ),
        );
    }

    /**
     * Test the implementation of all method from abstract repository.
     *
     * @return void
     */
    public function test_all_returns_collection(): void
    {
        $this->createDummyUsers(3);

        $result = $this->service->all();

        $this->assertInstanceOf(Collection::class, $result);

        $this->assertCount(3, $result);
    }

    /**
     * Test the implementation of create method from abstract repository.
     *
     * @return void
     */
    public function test_create_saves_and_returns_model(): void
    {
        $data = [
            'name' => $name = fake()->name(),
            'password' => $password = fake()->word(),
            'email' => $email = fake()->unique()->safeEmail(),
            'nickname' => $nickname = fake()->unique()->userName(),
            'level_id' => $levelId = Level::firstOrFail()->value('id'),
            'birthdate' => $birthdate = Carbon::today()->subYears(14)->toDateString(),
        ];

        $result = $this->service->create($data);

        $this->assertInstanceOf(User::class, $result);

        $this->assertDatabaseHas('users', [
            'name' => $name,
            'email' => $email,
            'level_id' => $levelId,
            'nickname' => $nickname,
            'birthdate' => $birthdate,
        ]);

        $this->assertTrue(Hash::check($password, (string)$result->password));
    }

    /**
     * Test if can find and returns correct model.
     *
     * @return void
     */
    public function test_find_returns_correct_model(): void
    {
        $user = $this->createDummyUser();

        $result = $this->service->find($user->id);

        $this->assertInstanceOf(User::class, $result);

        $this->assertEquals($user->id, $result->id);
    }

    /**
     * Test if can throw exception for invalid id on find or fail method implementation.
     *
     * @return void
     */
    public function test_find_or_fail_throws_exception_for_invalid_id(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->service->findOrFail(9999);
    }

    /**
     * Test if can update a model and can modifies and return correct update model on method implementation.
     *
     * @return void
     */
    public function test_update_modifies_and_returns_model(): void
    {
        $user = $this->createDummyUser();

        $updatedData = ['name' => 'Updated Name'];

        $result = $this->service->update($updatedData, $user->id);

        $this->assertInstanceOf(User::class, $result);

        $this->assertEquals('Updated Name', $result->name);

        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Updated Name']);
    }

    /**
     * Test if can soft deletes a model on method implementation.
     *
     * @return void
     */
    public function test_if_can_soft_deletes_a_model(): void
    {
        $user = $this->createDummyUser();

        $this->assertNotSoftDeleted($user);

        $this->service->delete($user->id);

        $this->assertSoftDeleted($user);
    }
}
