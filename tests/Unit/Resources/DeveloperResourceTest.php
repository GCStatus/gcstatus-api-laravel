<?php

namespace Tests\Unit\Resources;

use Mockery;
use App\Models\Developer;
use App\Http\Resources\DeveloperResource;
use Tests\Contracts\Resources\BaseResourceTesting;

class DeveloperResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for DeveloperResource.
     *
     * @var array<string, string>
     */
    protected array $expectedStructure = [
        'id' => 'int',
        'slug' => 'string',
        'name' => 'string',
        'acting' => 'bool',
    ];

    /**
     * Get the resource class being tested.
     *
     * @return class-string<DeveloperResource>
     */
    public function resource(): string
    {
        return DeveloperResource::class;
    }

    /**
     * Provide a mock instance of Developer for testing.
     *
     * @return \App\Models\Developer
     */
    public function modelInstance(): Developer
    {
        $developerMock = Mockery::mock(Developer::class)->makePartial();
        $developerMock->shouldAllowMockingMethod('getAttribute');

        $developerMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $developerMock->shouldReceive('getAttribute')->with('name')->andReturn(fake()->name());
        $developerMock->shouldReceive('getAttribute')->with('slug')->andReturn(fake()->name());
        $developerMock->shouldReceive('getAttribute')->with('acting')->andReturn(fake()->boolean());

        /** @var \App\Models\Developer $developerMock */
        return $developerMock;
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
