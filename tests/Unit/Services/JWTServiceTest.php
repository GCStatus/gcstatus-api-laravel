<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use Tymon\JWTAuth\JWT;
use App\Contracts\Services\JWTServiceInterface;

class JWTServiceTest extends TestCase
{
    /**
     * The jwt service.
     *
     * @var \App\Contracts\Services\JWTServiceInterface
     */
    private JWTServiceInterface $jwtService;

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

        $this->jwtService = app(JWTServiceInterface::class);
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
        $token = $this->jwtService->tokenize($userMock);

        $this->jwtHelper->setToken($token);
        $this->assertTrue($this->jwtHelper->check());
        $this->assertNotNull($this->jwtHelper->getToken());
    }

    /**
     * Test if can decode the user token.
     *
     * @return void
     */
    public function test_if_can_decode_the_user_token(): void
    {
        $this->assertFalse($this->jwtHelper->check());
        $this->assertNull($this->jwtHelper->getToken());

        $userMock = Mockery::mock(User::class)->makePartial();
        $userMock->shouldAllowMockingMethod('setAttribute');
        $userMock->shouldReceive('getAttribute')->with('id')->andReturn(1);

        /** @var \App\Models\User $userMock */
        $token = $this->jwtService->tokenize($userMock);

        $this->jwtHelper->setToken($token);

        $user = $this->jwtService->decode($token);

        $this->assertEquals($user['sub'], 1);
    }
}
