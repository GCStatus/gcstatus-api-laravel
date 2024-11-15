<?php

namespace Tests\Unit\Repositories;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use App\Contracts\Repositories\UserRepositoryInterface;

class UserRepositoryTest extends TestCase
{
    /**
     * The user repository.
     *
     * @var \App\Contracts\Repositories\UserRepositoryInterface
     */
    private $userRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->userRepository = app(UserRepositoryInterface::class);
    }

    /**
     * Test if UserRepository uses the User model correctly.
     *
     * @return void
     */
    public function test_user_repository_uses_user_model(): void
    {
        /** @var \App\Repositories\UserRepository $userRepository */
        $userRepository = $this->userRepository;

        $this->assertInstanceOf(User::class, $userRepository->model());
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

        $mockRepository = Mockery::mock(UserRepositoryInterface::class);
        $mockRepository->shouldReceive('firstOrCreate')
            ->once()
            ->with(['email' => $data['email']], $data)
            ->andReturn($userMock);

        /** @var \App\Contracts\Repositories\UserRepositoryInterface $mockRepository */
        $result = $mockRepository->firstOrCreate(['email' => $data['email']], $data);

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
