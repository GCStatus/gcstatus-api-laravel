<?php

namespace Tests\Unit\Resources;

use Mockery;
use App\Models\Critic;
use App\Http\Resources\CriticResource;
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
