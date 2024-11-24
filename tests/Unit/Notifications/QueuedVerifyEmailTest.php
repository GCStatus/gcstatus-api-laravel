<?php

namespace Tests\Unit\Notifications;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use App\Notifications\QueuedVerifyEmail;
use Illuminate\Support\Facades\Notification;

class QueuedVerifyEmailTest extends TestCase
{
    /**
     * Test if reset password notification is sent.
     *
     * @return void
     */
    public function test_if_can_send_reset_password_notification(): void
    {
        Notification::fake();

        $user = Mockery::mock(User::class)->makePartial();

        /** @var \App\Models\User $user */
        $user->notify(new QueuedVerifyEmail());

        Notification::assertSentTo(
            $user,
            QueuedVerifyEmail::class,
            fn () => true,
        );
    }

    /**
     * Test if content of reset password notification is correct.
     *
     * @return void
     */
    public function test_if_content_of_reset_password_notification_is_correct(): void
    {
        Notification::fake();

        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('getKey')->andReturn(1);
        $user->shouldReceive('getAttribute')->with('id')->once()->andReturn(1);
        $user->shouldReceive('getAttribute')->with('email')->twice()->andReturn('valid@gmail.com');

        /** @var \App\Models\User $user */
        $user->notify(new QueuedVerifyEmail());

        Notification::assertSentTo(
            $user,
            QueuedVerifyEmail::class,
            function ($notification) use ($user) {
                $url = $notification->toMail($user)->actionUrl;

                /** @var string $baseUrl */
                $baseUrl = config('app.url');

                $this->assertStringContainsString($baseUrl, $url);
                $this->assertStringContainsString((string)$user->id, $url);
                $this->assertStringContainsString(sha1($user->email), $url);

                return true;
            }
        );
    }


    /**
     * Test custom create URL callback.
     *
     * @return void
     */
    public function test_custom_create_url_callback_is_used(): void
    {
        $user = $this->getMockBuilder(User::class)
            ->onlyMethods(['getKey', 'getEmailForVerification'])
            ->getMock();

        $user->method('getKey')->willReturn(1);
        $user->method('getEmailForVerification')->willReturn('valid@gmail.com');

        QueuedVerifyEmail::createUrlUsing(function ($notifiable) {
            $this->assertInstanceOf(User::class, $notifiable);

            return 'https://custom-url.com/verification/' . $notifiable->getKey();
        });

        $notification = new QueuedVerifyEmail();

        $url = $notification->toMail($user)->actionUrl;

        $this->assertEquals('https://custom-url.com/verification/1', $url);
    }

    /**
     * Tear down test environments.
     *
     * @return void
     */
    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
