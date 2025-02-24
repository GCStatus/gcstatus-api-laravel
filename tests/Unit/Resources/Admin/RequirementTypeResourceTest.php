<?php

namespace Tests\Unit\Resources\Admin;

use Mockery;
use App\Models\RequirementType;
use Tests\Contracts\Resources\BaseResourceTesting;
use App\Http\Resources\Admin\RequirementTypeResource;

class RequirementTypeResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for RequirementTypeResource.
     *
     * @var array<string, string>
     */
    protected array $expectedStructure = [
        'id' => 'int',
        'os' => 'string',
        'potential' => 'string',
        'created_at' => 'string',
        'updated_at' => 'string',
    ];

    /**
     * Get the resource class being tested.
     *
     * @return class-string<RequirementTypeResource>
     */
    public function resource(): string
    {
        return RequirementTypeResource::class;
    }

    /**
     * Provide a mock instance of RequirementType for testing.
     *
     * @return \App\Models\RequirementType
     */
    public function modelInstance(): RequirementType
    {
        $requirementTypeMock = Mockery::mock(RequirementType::class)->makePartial();
        $requirementTypeMock->shouldAllowMockingMethod('getAttribute');

        $requirementTypeMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $requirementTypeMock->shouldReceive('getAttribute')->with('os')->andReturn(fake()->name());
        $requirementTypeMock->shouldReceive('getAttribute')->with('potential')->andReturn(fake()->name());
        $requirementTypeMock->shouldReceive('getAttribute')->with('created_at')->andReturn(fake()->date());
        $requirementTypeMock->shouldReceive('getAttribute')->with('updated_at')->andReturn(fake()->date());

        /** @var \App\Models\RequirementType $requirementTypeMock */
        return $requirementTypeMock;
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
