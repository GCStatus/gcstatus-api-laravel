<?php

namespace Tests\Unit\Resources;

use Mockery;
use App\Http\Resources\RequirementableResource;
use App\Models\{Requirementable, RequirementType};
use Tests\Contracts\Resources\BaseResourceTesting;

class RequirementableResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for RequirementableResource.
     *
     * @var array<string, string>
     */
    protected array $expectedStructure = [
        'id' => 'int',
        'os' => 'string',
        'dx' => 'string',
        'cpu' => 'string',
        'gpu' => 'string',
        'ram' => 'string',
        'rom' => 'string',
        'obs' => 'string',
        'network' => 'string',
        'type' => 'object',
    ];

    /**
     * Get the resource class being tested.
     *
     * @return class-string<RequirementableResource>
     */
    public function resource(): string
    {
        return RequirementableResource::class;
    }

    /**
     * Provide a mock instance of Requirementable for testing.
     *
     * @return \App\Models\Requirementable
     */
    public function modelInstance(): Requirementable
    {
        $requirementTypeMock = Mockery::mock(RequirementType::class)->makePartial();

        $requirementableMock = Mockery::mock(Requirementable::class)->makePartial();
        $requirementableMock->shouldAllowMockingMethod('getAttribute');

        $requirementableMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $requirementableMock->shouldReceive('getAttribute')->with('os')->andReturn(fake()->word());
        $requirementableMock->shouldReceive('getAttribute')->with('dx')->andReturn(fake()->word());
        $requirementableMock->shouldReceive('getAttribute')->with('cpu')->andReturn(fake()->word());
        $requirementableMock->shouldReceive('getAttribute')->with('gpu')->andReturn(fake()->word());
        $requirementableMock->shouldReceive('getAttribute')->with('ram')->andReturn(fake()->word());
        $requirementableMock->shouldReceive('getAttribute')->with('rom')->andReturn(fake()->word());
        $requirementableMock->shouldReceive('getAttribute')->with('obs')->andReturn(fake()->word());
        $requirementableMock->shouldReceive('getAttribute')->with('network')->andReturn(fake()->word());

        $requirementableMock->shouldReceive('getAttribute')->with('requirementType')->andReturn($requirementTypeMock);

        /** @var \App\Models\Requirementable $requirementableMock */
        return $requirementableMock;
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
