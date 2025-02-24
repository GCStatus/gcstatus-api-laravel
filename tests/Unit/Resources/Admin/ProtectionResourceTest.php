<?php

namespace Tests\Unit\Resources\Admin;

use Mockery;
use App\Models\Protection;
use App\Http\Resources\Admin\ProtectionResource;
use Tests\Contracts\Resources\BaseResourceTesting;

class ProtectionResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for ProtectionResource.
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
     * @return class-string<ProtectionResource>
     */
    public function resource(): string
    {
        return ProtectionResource::class;
    }

    /**
     * Provide a mock instance of Protection for testing.
     *
     * @return \App\Models\Protection
     */
    public function modelInstance(): Protection
    {
        $protectionMock = Mockery::mock(Protection::class)->makePartial();
        $protectionMock->shouldAllowMockingMethod('getAttribute');

        $protectionMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $protectionMock->shouldReceive('getAttribute')->with('name')->andReturn(fake()->name());
        $protectionMock->shouldReceive('getAttribute')->with('slug')->andReturn(fake()->name());
        $protectionMock->shouldReceive('getAttribute')->with('created_at')->andReturn(fake()->date());
        $protectionMock->shouldReceive('getAttribute')->with('updated_at')->andReturn(fake()->date());

        /** @var \App\Models\Protection $protectionMock */
        return $protectionMock;
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
