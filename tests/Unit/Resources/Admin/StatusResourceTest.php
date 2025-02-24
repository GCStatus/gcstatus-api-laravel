<?php

namespace Tests\Unit\Resources\Admin;

use Mockery;
use App\Models\Status;
use App\Http\Resources\Admin\StatusResource;
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
        'created_at' => 'string',
        'updated_at' => 'string',
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
        $statusMock->shouldReceive('getAttribute')->with('created_at')->andReturn(fake()->date());
        $statusMock->shouldReceive('getAttribute')->with('updated_at')->andReturn(fake()->date());

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
