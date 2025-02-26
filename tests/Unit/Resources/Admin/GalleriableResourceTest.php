<?php

namespace Tests\Unit\Resources\Admin;

use Mockery;
use App\Models\{MediaType, Galleriable};
use App\Http\Resources\Admin\GalleriableResource;
use Tests\Contracts\Resources\BaseResourceTesting;
use App\Contracts\Services\StorageServiceInterface;

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
        'created_at' => 'string',
        'updated_at' => 'string',
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
        $path = fake()->imageUrl();

        $storage = Mockery::mock(StorageServiceInterface::class);
        $storage->shouldReceive('getPath')->once()->with($path)->andReturn($path . '/s3');

        $this->app->instance(StorageServiceInterface::class, $storage);

        $mediaTypeMock = Mockery::mock(MediaType::class)->makePartial();

        $galleriableMock = Mockery::mock(Galleriable::class)->makePartial();
        $galleriableMock->shouldAllowMockingMethod('getAttribute');

        $galleriableMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $galleriableMock->shouldReceive('getAttribute')->with('s3')->andReturnTrue();
        $galleriableMock->shouldReceive('getAttribute')->with('path')->andReturn($path);
        $galleriableMock->shouldReceive('getAttribute')->with('created_at')->andReturn(fake()->date());
        $galleriableMock->shouldReceive('getAttribute')->with('updated_at')->andReturn(fake()->date());

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
