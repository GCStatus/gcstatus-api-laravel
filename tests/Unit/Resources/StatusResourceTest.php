<?php

namespace Tests\Unit\Resources;

use Mockery;
use App\Models\Status;
use App\Http\Resources\StatusResource;
use Tests\Contracts\Resources\BaseResourceTesting;

class StatusResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for StatusResource.
     *
     * @var array<string, string>
     */
    protected array $expectedStructure = [
        'id' => 'int',
        'name' => 'string',
    ];

    /**
     * Get the resource class being tested.
     *
     * @return class-string<StatusResource>
     */
    public function resource(): string
    {
        return StatusResource::class;
    }

    /**
     * Provide a mock instance of Status for testing.
     *
     * @return \App\Models\Status
     */
    public function modelInstance(): Status
    {
        $statusMock = Mockery::mock(Status::class)->makePartial();
        $statusMock->shouldAllowMockingMethod('getAttribute');

        $statusMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $statusMock->shouldReceive('getAttribute')->with('name')->andReturn(fake()->word());

        /** @var \App\Models\Status $statusMock */
        return $statusMock;
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
