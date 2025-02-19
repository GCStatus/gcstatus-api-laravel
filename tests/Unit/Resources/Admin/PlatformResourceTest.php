<?php

namespace Tests\Unit\Resources\Admin;

use Mockery;
use App\Models\Platform;
use App\Http\Resources\Admin\PlatformResource;
use Tests\Contracts\Resources\BaseResourceTesting;

class PlatformResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for PlatformResource.
     *
     * @var array<string, string>
     */
    protected array $expectedStructure = [
        'id' => 'int',
        'slug' => 'string',
        'name' => 'string',
        'created_at' => 'string',
        'updated_at' => 'string',
    ];

    /**
     * Get the resource class being tested.
     *
     * @return class-string<PlatformResource>
     */
    public function resource(): string
    {
        return PlatformResource::class;
    }

    /**
     * Provide a mock instance of Platform for testing.
     *
     * @return \App\Models\Platform
     */
    public function modelInstance(): Platform
    {
        $platformMock = Mockery::mock(Platform::class)->makePartial();
        $platformMock->shouldAllowMockingMethod('getAttribute');

        $platformMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $platformMock->shouldReceive('getAttribute')->with('name')->andReturn(fake()->name());
        $platformMock->shouldReceive('getAttribute')->with('slug')->andReturn(fake()->name());
        $platformMock->shouldReceive('getAttribute')->with('created_at')->andReturn(fake()->date());
        $platformMock->shouldReceive('getAttribute')->with('updated_at')->andReturn(fake()->date());

        /** @var \App\Models\Platform $platformMock */
        return $platformMock;
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
