<?php

namespace Tests\Unit\Repositories;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use Tymon\JWTAuth\JWT;
use App\Contracts\Repositories\JWTRepositoryInterface;
use RuntimeException;

class JWTRepositoryTest extends TestCase
{
    /**
     * The jwt repository.
     *
     * @var \App\Contracts\Repositories\JWTRepositoryInterface
     */
    private JWTRepositoryInterface $jwtRepository;

    /**
     * The jwt lib helpers.
     *
     * @var \Tymon\JWTAuth\JWT
     */
    private JWT $jwtHelper;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->jwtRepository = app(JWTRepositoryInterface::class);
        $this->jwtHelper = app(JWT::class);
    }

    /**
     * Test if can tokenize given user to jwt token.
     *
     * @return void
     */
    public function test_if_can_tokenize_given_user_to_jwt_token(): void
    {
        $this->assertFalse($this->jwtHelper->check());
        $this->assertNull($this->jwtHelper->getToken());

        $userMock = Mockery::mock(User::class)->makePartial();
        $userMock->shouldAllowMockingMethod('setAttribute');
        $userMock->shouldReceive('getAttribute')->with('id')->andReturn(1);

        /** @var \App\Models\User $userMock */
        $token = $this->jwtRepository->tokenize($userMock);

        $this->jwtHelper->setToken($token);
        $this->assertTrue($this->jwtHelper->check());
        $this->assertNotNull($this->jwtHelper->getToken());
    }

    /**
     * Test if can get the user by jwt token.
     *
     * @return void
     */
    public function test_if_can_get_the_user_by_jwt_token(): void
    {
        $userMock = Mockery::mock(User::class)->makePartial();
        $userMock->shouldAllowMockingMethod('setAttribute');
        $userMock->shouldReceive('getAttribute')->with('id')->andReturn(1);

        /** @var \App\Models\User $userMock */
        $token = $this->jwtRepository->tokenize($userMock);

        $this->jwtHelper->setToken($token);

        $user = $this->jwtRepository->decode($token);

        $this->assertEquals($user['sub'], 1);
    }

    /**
     * Test if can throw runtine exception if token validation fails.
     *
     * @return void
     */
    public function test_if_can_throw_runtime_validation_exception_if_token_validation_fails(): void
    {
        $token = 'invalid_token';
        $jwtRepository = Mockery::mock(JWTRepositoryInterface::class);

        $userMock = Mockery::mock(User::class)->makePartial();
        $userMock->shouldAllowMockingMethod('setAttribute');
        $userMock->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $jwtRepository
            ->shouldReceive('decode')
            ->once()
            ->with($token)
            ->andThrow(RuntimeException::class, 'Token validation failed');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Token validation failed');

        /** @var \App\Contracts\Repositories\JWTRepositoryInterface $jwtRepository */
        $user = $jwtRepository->decode($token);

        $this->assertEmpty($user);
    }
}
