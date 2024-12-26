<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use Mockery\MockInterface;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Exceptions\Password\CurrentPasswordDoesNotMatchException;
use App\Contracts\Services\{
    UserServiceInterface,
    HashServiceInterface,
    LevelServiceInterface,
};
use App\Notifications\DatabaseNotification;

class UserServiceTest extends TestCase
{
    /**
     * The user service.
     *
     * @var \App\Contracts\Services\UserServiceInterface
     */
    private UserServiceInterface $userService;

    /**
     * The user repository.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $userRepository;

    /**
     * The level service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $levelService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->levelService = Mockery::mock(LevelServiceInterface::class);
        $this->userRepository = Mockery::mock(UserRepositoryInterface::class);

        $this->app->instance(LevelServiceInterface::class, $this->levelService);
        $this->app->instance(UserRepositoryInterface::class, $this->userRepository);

        $this->userService = app(UserServiceInterface::class);
    }

    /**
     * Test if UserService uses the User model correctly.
     *
     * @return void
     */
    public function test_user_repository_uses_user_model(): void
    {
        $this->app->instance(UserRepositoryInterface::class, new UserRepository());

        /** @var \App\Services\UserService $userService */
        $userService = app(UserServiceInterface::class);

        $this->assertInstanceOf(UserRepository::class, $userService->repository());
    }

    /**
     * Test if can first or create the user.
     *
     * @return void
     */
    public function test_if_can_first_or_create_the_user(): void
    {
        $data = [
            'email' => 'test@example.com',
            'experience' => 0,
            'name' => 'Test User',
            'nickname' => 'testuser',
            'birthdate' => '2000-01-01',
        ];

        $userMock = Mockery::mock(User::class)->makePartial();
        $userMock->shouldAllowMockingMethod('setAttribute');

        $userMock->shouldReceive('getAttribute')->with('email')->andReturn($data['email']);
        $userMock->shouldReceive('getAttribute')->with('name')->andReturn($data['name']);

        $mockService = Mockery::mock(UserServiceInterface::class);
        $mockService->shouldReceive('firstOrCreate')
            ->once()
            ->with(['email' => $data['email']], $data)
            ->andReturn($userMock);

        /** @var \App\Contracts\Services\UserServiceInterface $mockService */
        $result = $mockService->firstOrCreate(['email' => $data['email']], $data);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($data['email'], $result->email);
        $this->assertEquals($data['name'], $result->name);
    }

    /**
     * Test if can update the user password.
     *
     * @return void
     */
    public function test_if_can_update_the_user_password(): void
    {
        $oldHashPassword = Hash::make('12345678');

        $data = [
            'old_password' => '12345678',
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => ']3N"g&D8pF7?',
        ];

        $userMock = Mockery::mock(User::class)->makePartial();
        $userMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $userMock->shouldReceive('getAttribute')->with('password')->andReturn($oldHashPassword);
        $userMock->shouldReceive('update')
            ->once()
            ->with(['password' => $data['password']]);

        $mockHashService = Mockery::mock(HashServiceInterface::class);
        $mockHashService->shouldReceive('check')
            ->once()
            ->with($oldHashPassword, $data['old_password'])
            ->andReturnTrue();

        $this->app->instance(HashServiceInterface::class, $mockHashService);

        /** @var \App\Models\User $userMock */
        $this->userService->updatePassword($userMock, $data['old_password'], $data['password']);

        $this->assertEquals(2, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations match.');
    }

    /**
     * Test if can throw exception if password don't match on update password.
     *
     * @return void
     */
    public function test_if_can_throw_exception_if_password_dont_match_on_update_password(): void
    {
        $oldHashPassword = Hash::make('12345678');

        $data = [
            'old_password' => '123',
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => ']3N"g&D8pF7?',
        ];

        $userMock = Mockery::mock(User::class)->makePartial();
        $userMock->shouldReceive('getAttribute')->with('password')->andReturn($oldHashPassword);

        $userMock->shouldNotReceive('update');

        $mockHashService = Mockery::mock(HashServiceInterface::class);
        $mockHashService->shouldReceive('check')
            ->once()
            ->with($oldHashPassword, '123')
            ->andReturnFalse();

        $this->app->instance(HashServiceInterface::class, $mockHashService);

        $this->expectException(CurrentPasswordDoesNotMatchException::class);
        $this->expectExceptionMessage('Your password does not match.');

        /** @var \App\Models\User $userMock */
        $this->userService->updatePassword($userMock, $data['old_password'], $data['password']);
    }

    /**
     * Test if can update the user sensitive data.
     *
     * @return void
     */
    public function test_if_can_update_the_user_sensitive_data(): void
    {
        $password = Hash::make('12345678');

        $data = [
            'password' => '12345678',
            'email' => 'valid@gmail.com',
            'nickname' => fake()->userName(),
        ];

        $userMock = Mockery::mock(User::class)->makePartial();
        $userMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $userMock->shouldReceive('getAttribute')->with('password')->andReturn($password);
        $userMock->shouldReceive('update')
            ->once()
            ->with([
                'email' => $data['email'],
                'nickname' => $data['nickname'],
            ]);

        $mockHashService = Mockery::mock(HashServiceInterface::class);
        $mockHashService->shouldReceive('check')
            ->once()
            ->with($password, $data['password'])
            ->andReturnTrue();

        $this->app->instance(HashServiceInterface::class, $mockHashService);

        /** @var \App\Models\User $userMock */
        $this->userService->updateSensitives($userMock, $data);

        $this->assertEquals(2, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations match.');
    }

    /**
     * Test if can throw exception if password don't match on update sensitives.
     *
     * @return void
     */
    public function test_if_can_throw_exception_if_password_dont_match_on_update_sensitives(): void
    {
        $password = Hash::make('12345678');

        $data = [
            'password' => '123',
            'email' => 'valid@gmail.com',
            'nickname' => fake()->userName(),
        ];

        $userMock = Mockery::mock(User::class)->makePartial();
        $userMock->shouldReceive('getAttribute')->with('password')->andReturn($password);
        $userMock->shouldNotReceive('update');

        $mockHashService = Mockery::mock(HashServiceInterface::class);
        $mockHashService->shouldReceive('check')
            ->once()
            ->with($password, '123')
            ->andReturnFalse();

        $this->app->instance(HashServiceInterface::class, $mockHashService);

        $this->expectException(CurrentPasswordDoesNotMatchException::class);
        $this->expectExceptionMessage('Your password does not match.');

        /** @var \App\Models\User $userMock */
        $this->userService->updateSensitives($userMock, $data);
    }

    /**
     * Test if can add experience to the given user.
     *
     * @return void
     */
    public function test_if_can_add_experience_to_the_given_user(): void
    {
        $userId = 1;
        $amount = 100;

        $user = Mockery::mock(User::class);

        $user->shouldReceive('getAttribute')->with('id')->andReturn($userId);
        $user->shouldReceive('getAttribute')->with('level_id')->andReturn(1);
        $user->shouldReceive('getAttribute')->with('experience')->andReturn(0);

        $user->shouldReceive('notify')
            ->once()
            ->with(Mockery::on(function (DatabaseNotification $notification) use ($user, $amount) {
                /** @var \App\Models\User $user */
                return $notification->data['userId'] === (string)$user->id &&
                    $notification->data['actionUrl'] === '/profile' &&
                    $notification->data['title'] === "You received $amount experience.";
            }));

        $this->userRepository
            ->shouldReceive('addExperience')
            ->once()
            ->with($user, $amount);

        $this->levelService
            ->shouldReceive('handleLevelUp')
            ->once()
            ->with($user);

        /** @var \App\Models\User $user */
        $this->userService->addExperience($user, $amount);

        $this->assertEquals(3, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations match.');
    }

    /**
     * Cleanup Mockery after each test.
     *
     * @return void
     */
    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
