<?php

namespace Tests\Unit\Resources;

use Mockery;
use App\Models\Publisher;
use App\Http\Resources\PublisherResource;
use Tests\Contracts\Resources\BaseResourceTesting;

class PublisherResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for PublisherResource.
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
     * @return class-string<PublisherResource>
     */
    public function resource(): string
    {
        return PublisherResource::class;
    }

    /**
     * Provide a mock instance of Publisher for testing.
     *
     * @return \App\Models\Publisher
     */
    public function modelInstance(): Publisher
    {
        $publisherMock = Mockery::mock(Publisher::class)->makePartial();
        $publisherMock->shouldAllowMockingMethod('getAttribute');

        $publisherMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $publisherMock->shouldReceive('getAttribute')->with('name')->andReturn(fake()->name());
        $publisherMock->shouldReceive('getAttribute')->with('slug')->andReturn(fake()->name());
        $publisherMock->shouldReceive('getAttribute')->with('acting')->andReturn(fake()->boolean());

        /** @var \App\Models\Publisher $publisherMock */
        return $publisherMock;
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
