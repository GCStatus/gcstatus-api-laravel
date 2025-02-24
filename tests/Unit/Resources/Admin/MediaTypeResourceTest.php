<?php

namespace Tests\Unit\Resources\Admin;

use Mockery;
use App\Models\MediaType;
use App\Http\Resources\Admin\MediaTypeResource;
use Tests\Contracts\Resources\BaseResourceTesting;

class MediaTypeResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for MediaTypeResource.
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
     * @return class-string<MediaTypeResource>
     */
    public function resource(): string
    {
        return MediaTypeResource::class;
    }

    /**
     * Provide a mock instance of MediaType for testing.
     *
     * @return \App\Models\MediaType
     */
    public function modelInstance(): MediaType
    {
        $mediaTypeMock = Mockery::mock(MediaType::class)->makePartial();
        $mediaTypeMock->shouldAllowMockingMethod('getAttribute');

        $mediaTypeMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $mediaTypeMock->shouldReceive('getAttribute')->with('name')->andReturn(fake()->name());
        $mediaTypeMock->shouldReceive('getAttribute')->with('created_at')->andReturn(fake()->date());
        $mediaTypeMock->shouldReceive('getAttribute')->with('updated_at')->andReturn(fake()->date());

        /** @var \App\Models\MediaType $mediaTypeMock */
        return $mediaTypeMock;
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
