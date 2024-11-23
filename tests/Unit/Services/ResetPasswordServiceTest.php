<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use Mockery\MockInterface;
use App\Services\ResetPasswordService;
use App\Notifications\PasswordReseted;
use Illuminate\Support\Facades\Notification;
use App\Contracts\Repositories\ResetPasswordRepositoryInterface;
use App\Contracts\Services\{
    UserServiceInterface,
    ResetPasswordServiceInterface,
};
use App\Exceptions\ResetPassword\{
    InvalidTokenException,
    UserRecentlyCreatedTokenException,
};

class ResetPasswordServiceTest extends TestCase
{
    /**
     * The reset password service.
     *
     * @var \App\Contracts\Services\ResetPasswordServiceInterface
     */
    private ResetPasswordServiceInterface $resetPasswordService;

    /**
     * The mock reset password repository.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $resetPasswordRepository;

    /**
     * The mock user service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $userService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->userService = Mockery::mock(UserServiceInterface::class);
        $this->resetPasswordRepository = Mockery::mock(ResetPasswordRepositoryInterface::class);

        /** @var \App\Contracts\Services\UserServiceInterface $userService */
        $userService = $this->userService;

        /** @var \App\Contracts\Repositories\ResetPasswordRepositoryInterface $resetPasswordRepository */
        $resetPasswordRepository = $this->resetPasswordRepository;

        $this->app->instance(
            ResetPasswordServiceInterface::class,
            new ResetPasswordService(
                $userService,
                $resetPasswordRepository,
            ),
        );
    }

    /**
     * Test if can send the reset password notification.
     *
     * @return void
     */
    public function test_if_can_send_the_reset_password_notification(): void
    {
        $email = 'valid@gmail.com';
        $token = 'valid_reset_token';
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('getAttribute')->with('email')->andReturn($email);

        $this->userService
            ->shouldReceive('findBy')
            ->once()
            ->with('email', $email)
            ->andReturn($user);

        $this->resetPasswordRepository
            ->shouldReceive('createResetToken')
            ->once()
            ->with($user)
            ->andReturn($token);

        $this->resetPasswordRepository
            ->shouldReceive('recentlyCreatedToken')
            ->once()
            ->with($user)
            ->andReturnFalse();

        $user->shouldReceive('sendPasswordResetNotification')
            ->once()
            ->with($token);

        $this->resetPasswordService = app(ResetPasswordServiceInterface::class);
        $this->resetPasswordService->sendResetNotification([
            'email' => $email,
        ]);

        $this->assertEquals(4, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations executed.');
    }

    /**
     * Test if can throw exception of throttle limit if user recently created a token.
     *
     * @return void
     */
    public function test_if_can_throw_of_throttle_limit_if_user_recently_created_a_token(): void
    {
        $email = 'valid@gmail.com';
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('getAttribute')->with('email')->andReturn($email);

        $this->userService
            ->shouldReceive('findBy')
            ->once()
            ->with('email', $email)
            ->andReturn($user);

        $this->resetPasswordRepository
            ->shouldReceive('recentlyCreatedToken')
            ->once()
            ->with($user)
            ->andReturnTrue();

        $this->expectException(UserRecentlyCreatedTokenException::class);
        $this->expectExceptionMessage('You must wait a few seconds to request a password reset again.');

        $this->resetPasswordService = app(ResetPasswordServiceInterface::class);
        $this->resetPasswordService->sendResetNotification([
            'email' => $email,
        ]);
    }

    /**
     * Test if can reset password.
     *
     * @return void
     */
    public function test_if_can_reset_password(): void
    {
        Notification::fake();

        $email = 'valid@gmail.com';
        $token = 'valid_reset_token';
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('getAttribute')->with('email')->andReturn($email);

        /** @var array<string, string> $data */
        $data = [
            'email' => $email,
            'token' => $token,
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => ']3N"g&D8pF7?',
        ];

        $this->userService
            ->shouldReceive('findBy')
            ->once()
            ->with('email', $email)
            ->andReturn($user);

        $this->resetPasswordRepository
            ->shouldReceive('exists')
            ->once()
            ->with($user, $token)
            ->andReturnTrue();

        $this->resetPasswordRepository
            ->shouldReceive('delete')
            ->once()
            ->with($user);

        $user->shouldReceive('update')
            ->once()
            ->with(['password' => $data['password']]);

        $user->shouldReceive('notify')
            ->once()
            ->with(PasswordReseted::class);

        $this->resetPasswordService = app(ResetPasswordServiceInterface::class);
        $this->resetPasswordService->resetPassword($data);

        $this->assertEquals(5, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations executed.');
    }

    /**
     * Test if can throw exception if token doesn't exists.
     *
     * @return void
     */
    public function test_if_can_throw_exception_if_token_doesnt_exist(): void
    {
        $email = 'valid@gmail.com';
        $token = 'valid_reset_token';
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('getAttribute')->with('email')->andReturn($email);

        /** @var array<string, string> $data */
        $data = [
            'email' => $email,
            'token' => $token,
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => ']3N"g&D8pF7?',
        ];

        $this->userService
            ->shouldReceive('findBy')
            ->once()
            ->with('email', $email)
            ->andReturn($user);

        $this->resetPasswordRepository
            ->shouldReceive('exists')
            ->once()
            ->with($user, $token)
            ->andReturnFalse();

        $this->expectException(InvalidTokenException::class);
        $this->expectExceptionMessage('We could not validate your reset password request. Please, try again later.');

        $this->resetPasswordService = app(ResetPasswordServiceInterface::class);
        $this->resetPasswordService->resetPassword($data);
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
