<?php

namespace Tests\Unit\Resources;

use Mockery;
use App\Models\UserMission;
use App\Http\Resources\UserMissionResource;
use Tests\Contracts\Resources\BaseResourceTesting;

class UserMissionResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for UserMissionResource.
     *
     * @var array<string, string>
     */
    protected array $expectedStructure = [
        'id' => 'int',
        'completed' => 'bool',
        'last_completed_at' => 'string',
        'user' => 'object',
        'mission' => 'object',
    ];

    /**
     * Get the resource class being tested.
     *
     * @return class-string<UserMissionResource>
     */
    public function resource(): string
    {
        return UserMissionResource::class;
    }

    /**
     * Provide a mock instance of UserMission for testing.
     *
     * @return \App\Models\UserMission
     */
    public function modelInstance(): UserMission
    {
        $userMissionMock = Mockery::mock(UserMission::class)->makePartial();
        $userMissionMock->shouldAllowMockingMethod('getAttribute');

        $userMissionMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $userMissionMock->shouldReceive('getAttribute')->with('completed')->andReturn(fake()->boolean());
        $userMissionMock->shouldReceive('getAttribute')->with('last_completed_at')->andReturn(fake()->date());

        /** @var \App\Models\UserMission $userMissionMock */
        return $userMissionMock;
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
