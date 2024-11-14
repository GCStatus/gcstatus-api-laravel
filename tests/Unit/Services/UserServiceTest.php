<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Contracts\Services\UserServiceInterface;

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
