<?php

namespace Tests\Unit\Resources;

use Mockery;
use App\Models\{Store, Storeable};
use App\Http\Resources\StoreableResource;
use Tests\Contracts\Resources\BaseResourceTesting;

class StoreableResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for StoreableResource.
     *
     * @var array<string, string>
     */
    protected array $expectedStructure = [
        'id' => 'int',
        'price' => 'int',
        'url' => 'string',
        'store_item_id' => 'string',
        'store' => 'object',
    ];

    /**
     * Get the resource class being tested.
     *
     * @return class-string<StoreableResource>
     */
    public function resource(): string
    {
        return StoreableResource::class;
    }

    /**
     * Provide a mock instance of Storeable for testing.
     *
     * @return \App\Models\Storeable
     */
    public function modelInstance(): Storeable
    {
        $storeMock = Mockery::mock(Store::class)->makePartial();

        $storeableMock = Mockery::mock(Storeable::class)->makePartial();
        $storeableMock->shouldAllowMockingMethod('getAttribute');

        $storeableMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $storeableMock->shouldReceive('getAttribute')->with('url')->andReturn(fake()->url());
        $storeableMock->shouldReceive('getAttribute')->with('price')->andReturn(fake()->numberBetween(100, 999));
        $storeableMock->shouldReceive('getAttribute')->with('store_item_id')->andReturn((string)fake()->numberBetween(1000000, 9999999));

        $storeableMock->shouldReceive('getAttribute')->with('store')->andReturn($storeMock);

        /** @var \App\Models\Storeable $storeableMock */
        return $storeableMock;
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
