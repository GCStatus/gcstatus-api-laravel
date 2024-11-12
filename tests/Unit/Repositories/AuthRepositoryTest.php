<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
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
}
