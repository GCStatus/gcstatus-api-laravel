<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\{Channel, InteractsWithSockets};

class DatabaseNotification extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;
    use Dispatchable;
    use SerializesModels;
    use InteractsWithSockets;

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
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param object $notifiable
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'icon' => $this->data['icon'],
            'title' => $this->data['title'],
            'actionUrl' => $this->data['actionUrl'],
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @param object $notifiable
     * @return array<string, mixed>
     */
    public function toBroadcast(object $notifiable): array
    {
        return [
            'data' => $this->data,
            'extra' => [
                'notification_id' => $this->id,
                'timestamp' => now()->toISOString(),
            ],
        ];
    }

    /**
     * The broadcast event name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'notification.created';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        /** @var string $userId */
        $userId = $this->data['userId'];

        return [
            new Channel("App.Models.User.$userId"),
        ];
    }
}
