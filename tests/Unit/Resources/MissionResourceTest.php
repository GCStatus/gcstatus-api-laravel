<?php

namespace Tests\Unit\Resources;

use Mockery;
use App\Models\Mission;
use App\Http\Resources\MissionResource;
use Tests\Contracts\Resources\BaseResourceTesting;

class MissionResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for MissionResource.
     *
     * @var array<string, string>
     */
    protected array $expectedStructure = [
        'id' => 'int',
        'coins' => 'int',
        'for_all' => 'bool',
        'status' => 'object',
        'mission' => 'string',
        'experience' => 'int',
        'frequency' => 'string',
        'description' => 'string',
        'progress' => 'object',
        'rewards' => 'resourceCollection',
        'requirements' => 'resourceCollection',
    ];

    /**
     * Get the resource class being tested.
     *
     * @return class-string<MissionResource>
     */
    public function resource(): string
    {
        return MissionResource::class;
    }

    /**
     * Provide a mock instance of Mission for testing.
     *
     * @return \App\Models\Mission
     */
    public function modelInstance(): Mission
    {
        $missionMock = Mockery::mock(Mission::class)->makePartial();
        $missionMock->shouldAllowMockingMethod('getAttribute');

        $missionMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $missionMock->shouldReceive('getAttribute')->with('for_all')->andReturn(fake()->boolean());
        $missionMock->shouldReceive('getAttribute')->with('mission')->andReturn(fake()->realText());
        $missionMock->shouldReceive('getAttribute')->with('description')->andReturn(fake()->realText());
        $missionMock->shouldReceive('getAttribute')->with('coins')->andReturn(fake()->numberBetween(1, 9999));
        $missionMock->shouldReceive('getAttribute')->with('experience')->andReturn(fake()->numberBetween(1, 9999));
        $missionMock->shouldReceive('getAttribute')->with('frequency')->andReturn(fake()->randomElement(['one_time', 'daily', 'weekly', 'monthly', 'yearly']));

        /** @var \App\Models\Mission $missionMock */
        return $missionMock;
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
