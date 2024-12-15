<?php

namespace Tests\Notifications;

use Illuminate\Queue\SerializesModels;
use Illuminate\Notifications\Notification;
use Illuminate\Foundation\Events\Dispatchable;

class DummyNotification extends Notification
{
    use Dispatchable;
    use SerializesModels;

    /**
     * The message data.
     *
     * @var array<string, string>
     */
    public $data;

    /**
     * Create a new notification instance.
     *
     * @param array<string, string> $data
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->data;
    }
}
