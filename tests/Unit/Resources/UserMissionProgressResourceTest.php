<?php

namespace Tests\Unit\Resources;

use Mockery;
use App\Models\UserMissionProgress;
use Tests\Contracts\Resources\BaseResourceTesting;
use App\Http\Resources\UserMissionProgressResource;

class UserMissionProgressResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for UserMissionProgressResource.
     *
     * @var array<string, string>
     */
    protected array $expectedStructure = [
        'id' => 'int',
        'progress' => 'int',
        'completed' => 'bool',
        'user' => 'object',
        'requirement' => 'object',
    ];

    /**
     * Get the resource class being tested.
     *
     * @return class-string<UserMissionProgressResource>
     */
    public function resource(): string
    {
        return UserMissionProgressResource::class;
    }

    /**
     * Provide a mock instance of UserMissionProgress for testing.
     *
     * @return \App\Models\UserMissionProgress
     */
    public function modelInstance(): UserMissionProgress
    {
        $userMissionProgressMock = Mockery::mock(UserMissionProgress::class)->makePartial();
        $userMissionProgressMock->shouldAllowMockingMethod('getAttribute');

        $userMissionProgressMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $userMissionProgressMock->shouldReceive('getAttribute')->with('completed')->andReturn(fake()->boolean());
        $userMissionProgressMock->shouldReceive('getAttribute')->with('progress')->andReturn(fake()->numberBetween(1, 10));

        /** @var \App\Models\UserMissionProgress $userMissionProgressMock */
        return $userMissionProgressMock;
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
