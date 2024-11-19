<?php

namespace Tests\Unit\Repositories;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Contracts\Repositories\AuthRepositoryInterface;

class AuthRepositoryTest extends TestCase
{
    /**
     * The abstract repository.
     *
     * @var \App\Contracts\Repositories\AuthRepositoryInterface
     */
    private AuthRepositoryInterface $repository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->repository = app(AuthRepositoryInterface::class);
    }

    /**
     * Test if can authenticate a user by email.
     *
     * @return void
     */
    public function test_if_can_auth_an_user_by_email(): void
    {
        $fakeToken = 'q8bOzeTfTAxhE54vduOLzRM1NnetPOD5';

        Auth::shouldReceive('attempt')
            ->once()
            ->with([
                'email' => 'fake@email.com',
                'password' => 'admin1234',
            ])->andReturn($fakeToken);

        $result = $this->repository->authByEmail([
            'identifier' => 'fake@email.com',
            'password' => 'admin1234',
        ]);

        $this->assertSame($fakeToken, $result);
    }

    /**
     * Test if can authenticate a user by nickname.
     *
     * @return void
     */
    public function test_if_can_auth_an_user_by_nickname(): void
    {
        $fakeToken = 'q8bOzeTfTAxhE54vduOLzRM1NnetPOD5';

        Auth::shouldReceive('attempt')
            ->once()
            ->with([
                'nickname' => $userName = fake()->userName(),
                'password' => 'admin1234',
            ])->andReturn($fakeToken);

        $result = $this->repository->authByNickname([
            'identifier' => $userName,
            'password' => 'admin1234',
        ]);

        $this->assertSame($fakeToken, $result);
    }

    /**
     * Test if can get the auth user id.
     *
     * @return void
     */
    public function test_if_can_get_the_auth_user_id(): void
    {
        Auth::shouldReceive('id')
            ->once()
            ->andReturn(1);

        $result = $this->repository->getAuthId();

        $this->assertSame(1, $result);
    }

    /**
     * Test if can set the user on request.
     *
     * @return void
     */
    public function test_if_can_set_the_user_on_request(): void
    {
        $user = Mockery::mock(Authenticatable::class);

        Auth::shouldReceive('setUser')
            ->once()
            ->with($user);

        $this->repository->setUser($user);

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations executed.');
    }

    /**
     * Test if can authenticate user by id.
     *
     * @return void
     */
    public function test_if_can_authenticate_user_by_id(): void
    {
        Auth::shouldReceive('onceUsingId')
            ->once()
            ->with(1);

        $this->repository->authenticateById(1);

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations executed.');
    }

    /**
     * Test if can get auth user data.
     *
     * @return void
     */
    public function test_if_can_get_auth_user_data(): void
    {
        $user = Mockery::mock(User::class);

        Auth::shouldReceive('user')
            ->once()
            ->andReturn($user);

        $this->repository->getAuthUser();

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations executed.');
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
