<?php

namespace Tests\Unit\Repositories;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use Tymon\JWTAuth\JWT;
use App\Contracts\Repositories\JWTRepositoryInterface;

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
}
