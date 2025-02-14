<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\{User, Level};
use Illuminate\Support\Carbon;
use Tests\Traits\HasDummyUser;
use Database\Seeders\LevelSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Implementations\ConcreteAbstractRepository;
use App\Contracts\Repositories\AbstractRepositoryInterface;
use Illuminate\Database\Eloquent\{Collection, ModelNotFoundException};

class AbstractRepositoryTest extends TestCase
{
    use HasDummyUser;
    use RefreshDatabase;

    /**
     * The abstract repository.
     *
     * @var \App\Contracts\Repositories\AbstractRepositoryInterface
     */
    private AbstractRepositoryInterface $repository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->seed([LevelSeeder::class]);

        $this->repository = new ConcreteAbstractRepository(new User());
    }

    /**
     * Test the implementation of all method from abstract repository.
     *
     * @return void
     */
    public function test_all_returns_collection(): void
    {
        $this->createDummyUsers(3);

        $result = $this->repository->all();

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

        $result = $this->repository->create($data);

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
     * Test the implementation of first or create method from abstract repository.
     *
     * @return void
     */
    public function test_first_or_create_saves_and_returns_model(): void
    {
        $data = [
            'name' => $name = fake()->name(),
            'password' => $password = fake()->word(),
            'email' => $email = fake()->unique()->safeEmail(),
            'nickname' => $nickname = fake()->unique()->userName(),
            'level_id' => $levelId = Level::firstOrFail()->value('id'),
            'birthdate' => $birthdate = Carbon::today()->subYears(14)->toDateString(),
        ];

        $result = $this->repository->firstOrCreate($data);

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
     * Test the implementation of first or create can find user if exists method from abstract repository.
     *
     * @return void
     */
    public function test_first_or_create_can_find_and_returns_model(): void
    {
        $user = $this->createDummyUser();

        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'nickname' => $user->nickname,
            'birthdate' => $user->birthdate,
        ];

        $result = $this->repository->firstOrCreate($data);

        $this->assertInstanceOf(User::class, $result);

        $this->assertDatabaseHas('users', [
            'name' => $user->name,
            'email' => $user->email,
            'level_id' => $user->level_id,
            'nickname' => $user->nickname,
            'birthdate' => $user->birthdate,
        ]);
    }

    /**
     * Test if can find many models by ids.
     *
     * @return void
     */
    public function test_if_can_find_many_models_by_ids(): void
    {
        $users = $this->createDummyUsers(3);

        $this->createDummyUsers(3); // Assert this don't appears on tests.

        $ids = $users->pluck('id')->toArray();

        $result = $this->repository->findIn($ids);

        $this->assertInstanceOf(Collection::class, $result);

        $this->assertCount(3, $result);
    }

    /**
     * Test if can find and returns correct model.
     *
     * @return void
     */
    public function test_find_returns_correct_model(): void
    {
        $user = $this->createDummyUser();

        $result = $this->repository->find($user->id);

        $this->assertInstanceOf(User::class, $result);

        $this->assertEquals($user->id, $result->id);
    }

    /**
     * Test if can find by given field and returns correct model.
     *
     * @return void
     */
    public function test_find_by_returns_correct_model(): void
    {
        $user = $this->createDummyUser();

        $result = $this->repository->findBy('email', $user->email);

        $this->assertInstanceOf(User::class, $result);

        $this->assertEquals($user->id, $result->id);
    }

    /**
     * Test if can find all by given field and returns correct collection.
     *
     * @return void
     */
    public function test_find_all_by_returns_correct_collection(): void
    {
        $user = $this->createDummyUser();

        $result = $this->repository->findAllBy('email', $user->email);

        $this->assertInstanceOf(Collection::class, $result);

        $this->assertTrue(in_array($user->email, $result->pluck('email')->toArray()));
    }

    /**
     * Test if can throw exception for invalid id on find or fail method implementation.
     *
     * @return void
     */
    public function test_find_or_fail_throws_exception_for_invalid_id(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->repository->findOrFail(9999);
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

        $result = $this->repository->update($updatedData, $user->id);

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

        $this->repository->delete($user->id);

        $this->assertSoftDeleted($user);
    }
}
