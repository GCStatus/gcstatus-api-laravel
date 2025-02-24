<?php

namespace Tests\Unit\Resources\Admin;

use Mockery;
use App\Models\Critic;
use App\Http\Resources\Admin\CriticResource;
use Tests\Contracts\Resources\BaseResourceTesting;

class CriticResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for CriticResource.
     *
     * @var array<string, string>
     */
    protected array $expectedStructure = [
        'id' => 'int',
        'url' => 'string',
        'name' => 'string',
        'logo' => 'string',
        'slug' => 'string',
        'acting' => 'bool',
        'created_at' => 'string',
        'updated_at' => 'string',
    ];

    /**
     * Get the resource class being tested.
     *
     * @return class-string<CriticResource>
     */
    public function resource(): string
    {
        return CriticResource::class;
    }

    /**
     * Provide a mock instance of Critic for testing.
     *
     * @return \App\Models\Critic
     */
    public function modelInstance(): Critic
    {
        $CriticMock = Mockery::mock(Critic::class)->makePartial();
        $CriticMock->shouldAllowMockingMethod('getAttribute');

        $CriticMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $CriticMock->shouldReceive('getAttribute')->with('url')->andReturn(fake()->url());
        $CriticMock->shouldReceive('getAttribute')->with('name')->andReturn(fake()->name());
        $CriticMock->shouldReceive('getAttribute')->with('slug')->andReturn(fake()->name());
        $CriticMock->shouldReceive('getAttribute')->with('logo')->andReturn(fake()->imageUrl());
        $CriticMock->shouldReceive('getAttribute')->with('acting')->andReturn(fake()->boolean());
        $CriticMock->shouldReceive('getAttribute')->with('created_at')->andReturn(fake()->date());
        $CriticMock->shouldReceive('getAttribute')->with('updated_at')->andReturn(fake()->date());

        /** @var \App\Models\Critic $CriticMock */
        return $CriticMock;
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
