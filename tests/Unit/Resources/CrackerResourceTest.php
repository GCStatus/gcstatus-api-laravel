<?php

namespace Tests\Unit\Resources;

use Mockery;
use App\Models\Cracker;
use App\Http\Resources\CrackerResource;
use Tests\Contracts\Resources\BaseResourceTesting;

class CrackerResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for CrackerResource.
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
     * @return class-string<CrackerResource>
     */
    public function resource(): string
    {
        return CrackerResource::class;
    }

    /**
     * Provide a mock instance of Cracker for testing.
     *
     * @return \App\Models\Cracker
     */
    public function modelInstance(): Cracker
    {
        $crackerMock = Mockery::mock(Cracker::class)->makePartial();
        $crackerMock->shouldAllowMockingMethod('getAttribute');

        $crackerMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $crackerMock->shouldReceive('getAttribute')->with('name')->andReturn(fake()->name());
        $crackerMock->shouldReceive('getAttribute')->with('slug')->andReturn(fake()->name());
        $crackerMock->shouldReceive('getAttribute')->with('acting')->andReturn(fake()->boolean());

        /** @var \App\Models\Cracker $crackerMock */
        return $crackerMock;
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
