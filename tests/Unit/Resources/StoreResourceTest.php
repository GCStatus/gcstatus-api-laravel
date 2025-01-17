<?php

namespace Tests\Unit\Resources;

use Mockery;
use App\Models\Store;
use App\Http\Resources\StoreResource;
use Tests\Contracts\Resources\BaseResourceTesting;

class StoreResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for StoreResource.
     *
     * @var array<string, string>
     */
    protected array $expectedStructure = [
        'id' => 'int',
        'url' => 'string',
        'name' => 'string',
        'slug' => 'string',
        'logo' => 'string',
    ];

    /**
     * Get the resource class being tested.
     *
     * @return class-string<StoreResource>
     */
    public function resource(): string
    {
        return StoreResource::class;
    }

    /**
     * Provide a mock instance of Store for testing.
     *
     * @return \App\Models\Store
     */
    public function modelInstance(): Store
    {
        $storeMock = Mockery::mock(Store::class)->makePartial();
        $storeMock->shouldAllowMockingMethod('getAttribute');

        $storeMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $storeMock->shouldReceive('getAttribute')->with('url')->andReturn(fake()->url());
        $storeMock->shouldReceive('getAttribute')->with('name')->andReturn(fake()->name());
        $storeMock->shouldReceive('getAttribute')->with('slug')->andReturn(fake()->name());
        $storeMock->shouldReceive('getAttribute')->with('logo')->andReturn(fake()->imageUrl());

        /** @var \App\Models\Store $storeMock */
        return $storeMock;
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