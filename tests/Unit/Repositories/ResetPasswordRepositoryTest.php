<?php

namespace Tests\Unit\Repositories;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use App\Repositories\ResetPasswordRepository;

class ResetPasswordRepositoryTest extends TestCase
{
    /**
     * The reset password repository.
     *
     * @var \Mockery\MockInterface
     */
    private $resetPasswordRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->resetPasswordRepository = Mockery::mock(ResetPasswordRepository::class);
    }

    /**
     * Test if can create a reset password token.
     *
     * @return void
     */
    public function test_if_can_create_a_reset_password_token(): void
    {
        $token = 'valid_reset_token';
        $user = Mockery::mock(User::class)->makePartial();

        $this->resetPasswordRepository
            ->shouldReceive('createResetToken')
            ->once()
            ->with($user)
            ->andReturn($token);

        /** @var \App\Contracts\Repositories\ResetPasswordRepositoryInterface $resetPasswordRepository */
        $resetPasswordRepository = $this->resetPasswordRepository;

        /** @var \App\Models\User $user */
        $resetPasswordRepository->createResetToken($user);

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations executed.');
    }

    /**
     * Test if can check if a password reset token is recently created.
     *
     * @return void
     */
    public function test_if_can_check_if_a_password_reset_token_is_recently_created(): void
    {
        $user = Mockery::mock(User::class)->makePartial();

        $this->resetPasswordRepository
            ->shouldReceive('recentlyCreatedToken')
            ->once()
            ->with($user)
            ->andReturnTrue();

        /** @var \App\Contracts\Repositories\ResetPasswordRepositoryInterface $resetPasswordRepository */
        $resetPasswordRepository = $this->resetPasswordRepository;

        /** @var \App\Models\User $user */
        $result = $resetPasswordRepository->recentlyCreatedToken($user);

        $this->assertTrue($result);
    }

    /**
     * Test if can check if a token exists for given user.
     *
     * @return void
     */
    public function test_if_can_check_if_a_token_exists_for_given_user(): void
    {
        $token = 'fake_dummy_token';
        $user = Mockery::mock(User::class)->makePartial();

        $this->resetPasswordRepository
            ->shouldReceive('exists')
            ->once()
            ->with($user, $token)
            ->andReturnTrue();

        /** @var \App\Contracts\Repositories\ResetPasswordRepositoryInterface $resetPasswordRepository */
        $resetPasswordRepository = $this->resetPasswordRepository;

        /** @var \App\Models\User $user */
        $result = $resetPasswordRepository->exists($user, $token);

        $this->assertTrue($result);
    }

    /**
     * Test if can delete a token for given user.
     *
     * @return void
     */
    public function test_if_can_delete_a_token_for_given_user(): void
    {
        $user = Mockery::mock(User::class)->makePartial();

        $this->resetPasswordRepository
            ->shouldReceive('delete')
            ->once()
            ->with($user)
            ->andReturnTrue();

        /** @var \App\Contracts\Repositories\ResetPasswordRepositoryInterface $resetPasswordRepository */
        $resetPasswordRepository = $this->resetPasswordRepository;

        /** @var \App\Models\User $user */
        $resetPasswordRepository->delete($user);

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
