<?php

namespace Tests\Unit\Resources;

use Mockery;
use App\Models\{Critic, Criticable};
use App\Http\Resources\CriticableResource;
use Tests\Contracts\Resources\BaseResourceTesting;

class CriticableResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for CriticableResource.
     *
     * @var array<string, string>
     */
    protected array $expectedStructure = [
        'id' => 'int',
        'url' => 'string',
        'rate' => 'float',
        'posted_at' => 'string',
        'critic' => 'object',
    ];

    /**
     * Get the resource class being tested.
     *
     * @return class-string<CriticableResource>
     */
    public function resource(): string
    {
        return CriticableResource::class;
    }

    /**
     * Provide a mock instance of Criticable for testing.
     *
     * @return \App\Models\Criticable
     */
    public function modelInstance(): Criticable
    {
        $criticMock = Mockery::mock(Critic::class)->makePartial();

        $criticableMock = Mockery::mock(Criticable::class)->makePartial();
        $criticableMock->shouldAllowMockingMethod('getAttribute');

        $criticableMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $criticableMock->shouldReceive('getAttribute')->with('url')->andReturn(fake()->url());
        $criticableMock->shouldReceive('getAttribute')->with('posted_at')->andReturn(fake()->date());
        $criticableMock->shouldReceive('getAttribute')->with('rate')->andReturn(fake()->randomFloat());

        $criticableMock->shouldReceive('getAttribute')->with('critic')->andReturn($criticMock);

        /** @var \App\Models\Criticable $criticableMock */
        return $criticableMock;
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
