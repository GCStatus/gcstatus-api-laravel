<?php

namespace Tests\Unit\Resources;

use Mockery;
use App\Http\Resources\NotificationResource;
use Illuminate\Notifications\DatabaseNotification;
use Tests\Contracts\Resources\BaseResourceTesting;

class NotificationResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for NotificationResource.
     *
     * @var array<string, string>
     */
    protected array $expectedStructure = [
        'id' => 'string',
        'data' => 'object',
        'read_at' => 'string',
        'created_at' => 'string',
        'updated_at' => 'string',
    ];

    /**
     * Get the resource class being tested.
     *
     * @return class-string<NotificationResource>
     */
    public function resource(): string
    {
        return NotificationResource::class;
    }

    /**
     * Provide a mock instance of DatabaseNotification for testing.
     *
     * @return \Illuminate\Notifications\DatabaseNotification
     */
    public function modelInstance(): DatabaseNotification
    {
        $databaseNotificationMock = Mockery::mock(DatabaseNotification::class)->makePartial();
        $databaseNotificationMock->shouldAllowMockingMethod('getAttribute');

        $databaseNotificationMock->shouldReceive('getAttribute')->with('id')->andReturn(fake()->uuid());
        $databaseNotificationMock->shouldReceive('getAttribute')->with('data')->andReturn((object)[
            'icon' => 'n',
            'actionUrl' => 'https://google.com',
            'title' => 'Prof.',
            'content' => 'Hello!',
        ]);
        $databaseNotificationMock->shouldReceive('getAttribute')->with('read_at')->andReturn(now()->toISOString());
        $databaseNotificationMock->shouldReceive('getAttribute')->with('created_at')->andReturn(now()->toISOString());
        $databaseNotificationMock->shouldReceive('getAttribute')->with('updated_at')->andReturn(now()->toISOString());


        /** @var \Illuminate\Notifications\DatabaseNotification $databaseNotificationMock */
        return $databaseNotificationMock;
    }

    /**
     * Cleanup Mockery after each test.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
