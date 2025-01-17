<?php

namespace Tests\Unit\Resources;

use Mockery;
use App\Models\{MediaType, Galleriable};
use App\Http\Resources\GalleriableResource;
use Tests\Contracts\Resources\BaseResourceTesting;

class GalleriableResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for GalleriableResource.
     *
     * @var array<string, string>
     */
    protected array $expectedStructure = [
        'id' => 'int',
        'path' => 'string',
        'type' => 'object',
    ];

    /**
     * Get the resource class being tested.
     *
     * @return class-string<GalleriableResource>
     */
    public function resource(): string
    {
        return GalleriableResource::class;
    }

    /**
     * Provide a mock instance of Galleriable for testing.
     *
     * @return \App\Models\Galleriable
     */
    public function modelInstance(): Galleriable
    {
        $mediaTypeMock = Mockery::mock(MediaType::class)->makePartial();

        $galleriableMock = Mockery::mock(Galleriable::class)->makePartial();
        $galleriableMock->shouldAllowMockingMethod('getAttribute');

        $galleriableMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $galleriableMock->shouldReceive('getAttribute')->with('path')->andReturn(fake()->word());

        $galleriableMock->shouldReceive('getAttribute')->with('mediaType')->andReturn($mediaTypeMock);

        /** @var \App\Models\Galleriable $galleriableMock */
        return $galleriableMock;
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
