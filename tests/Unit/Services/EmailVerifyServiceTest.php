<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use App\Notifications\QueuedVerifyEmail;
use Illuminate\Support\Facades\Notification;
use App\Contracts\Services\EmailVerifyServiceInterface;
use App\Exceptions\EmailVerify\AlreadyVerifiedEmailException;

class EmailVerifyServiceTest extends TestCase
{
    /**
     * The email verify Service.
     *
     * @var \App\Contracts\Services\EmailVerifyServiceInterface
     */
    private EmailVerifyServiceInterface $emailVerifyService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->emailVerifyService = app(EmailVerifyServiceInterface::class);
    }

    /**
     * Test if can verify if user has verified email.
     *
     * @return void
     */
    public function test_if_can_verify_if_user_has_verified_email(): void
    {
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('hasVerifiedEmail')
            ->once()
            ->withNoArgs()
            ->andReturnTrue();

        /** @var \App\Models\User $user */
        $result = $this->emailVerifyService->verified($user);

        $this->assertTrue($result);
    }

    /**
     * Test if can verify user email.
     *
     * @return void
     */
    public function test_if_can_verify_user_email(): void
    {
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('hasVerifiedEmail')
            ->once()
            ->withNoArgs()
            ->andReturnFalse();
        $user->shouldReceive('markEmailAsVerified')
            ->once()
            ->withNoArgs()
            ->andReturnTrue();

        /** @var \App\Models\User $user */
        $result = $this->emailVerifyService->verify($user);

        $this->assertTrue($result);
    }

    /**
     * Test if can't verify user email if already verified.
     *
     * @return void
     */
    public function test_if_cant_verify_user_email_if_already_verified(): void
    {
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('hasVerifiedEmail')
            ->once()
            ->withNoArgs()
            ->andReturnTrue();
        $user->shouldNotReceive('markEmailAsVerified');

        $this->expectException(AlreadyVerifiedEmailException::class);
        $this->expectExceptionMessage('You already verified your email, no one more action is required.');

        /** @var \App\Models\User $user */
        $this->emailVerifyService->verify($user);
    }

    /**
     * Test if user email is marked as verified.
     *
     * @return void
     */
    public function test_if_user_email_is_marked_as_verified(): void
    {
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('markEmailAsVerified')
            ->once()
            ->withNoArgs()
            ->andReturnUsing(function () use ($user) {
                $user->shouldReceive('hasVerifiedEmail')
                    ->withNoArgs()
                    ->andReturnTrue();

                return true;
            });

        /** @var \App\Models\User $user */
        $result = $this->emailVerifyService->verify($user);

        $this->assertTrue($result);

        $user->refresh();
        $this->assertTrue($user->hasVerifiedEmail());
    }

    /**
     * Test if can notify user with email verification link if not verified yet.
     *
     * @return void
     */
    public function test_if_can_notify_user_with_email_verification_link_if_not_verified_yet(): void
    {
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('hasVerifiedEmail')
            ->once()
            ->withNoArgs()
            ->andReturnFalse();
        $user->shouldReceive('sendEmailVerificationNotification')
            ->once()
            ->withNoArgs();

        /** @var \App\Models\User $user */
        $this->emailVerifyService->notify($user);

        $this->assertEquals(2, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations executed.');
    }

    /**
     * Test if can't notify user with email verification link if already verified.
     *
     * @return void
     */
    public function test_if_cant_notify_user_with_email_verification_link_if_already_verified(): void
    {
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('hasVerifiedEmail')
            ->once()
            ->withNoArgs()
            ->andReturnTrue();
        $user->shouldNotReceive('sendEmailVerificationNotification');

        $this->expectException(AlreadyVerifiedEmailException::class);
        $this->expectExceptionMessage('You already verified your email, no one more action is required.');

        /** @var \App\Models\User $user */
        $this->emailVerifyService->notify($user);
    }

    /**
     * Test if can dispatch notification on email verify notify method.
     *
     * @return void
     */
    public function test_if_can_dispatch_notification_on_email_verify_notify_method(): void
    {
        Notification::fake();

        $user = Mockery::mock(User::class)->makePartial();

        $user->shouldReceive('hasVerifiedEmail')
            ->once()
            ->withNoArgs()
            ->andReturnFalse();

        /** @var \App\Models\User $user */
        $this->emailVerifyService->notify($user);

        Notification::assertSentTo($user, QueuedVerifyEmail::class, fn () => true);
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
