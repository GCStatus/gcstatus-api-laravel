<?php

namespace Tests\Unit\Services;

use App\Contracts\Services\HashServiceInterface;
use Mockery;
use Tests\TestCase;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Contracts\Services\UserServiceInterface;
use App\Exceptions\Password\CurrentPasswordDoesNotMatchException;
use Illuminate\Support\Facades\Hash;

class UserServiceTest extends TestCase
{
    /**
     * The user service.
     *
     * @var \App\Contracts\Services\UserServiceInterface
     */
    private $userService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->userService = app(UserServiceInterface::class);
    }

    /**
     * Test if UserService uses the User model correctly.
     *
     * @return void
     */
    public function test_user_repository_uses_user_model(): void
    {
        /** @var \App\Services\UserService $userService */
        $userService = $this->userService;

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
        $userMock->shouldReceive('getAttribute')->with('password')->andReturn($oldHashPassword);

        $userMock->shouldReceive('update')
            ->once()
            ->with(['password' => $data['password']]);

        $mockHashService = Mockery::mock(HashServiceInterface::class);
        $mockHashService->shouldReceive('check')
            ->once()
            ->with($oldHashPassword, '12345678')
            ->andReturnTrue();

        $this->app->instance(HashServiceInterface::class, $mockHashService);

        /** @var \App\Models\User $userMock */
        $userService = app(UserServiceInterface::class);
        $userService->updatePassword($userMock, $data['old_password'], $data['password']);

        $this->assertEquals(2, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations match.');
    }

    /**
     * Test if can throw exception if password don't match.
     *
     * @return void
     */
    public function test_if_can_throw_exception_if_password_dont_match(): void
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
        $this->expectExceptionMessage('Your current password does not match.');

        /** @var \App\Models\User $userMock */
        $userService = app(UserServiceInterface::class);
        $userService->updatePassword($userMock, $data['old_password'], $data['password']);
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
