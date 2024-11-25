<?php

namespace Tests\Contracts\Resources;

use Tests\TestCase;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

abstract class BaseResourceTesting extends TestCase implements ShouldTestResources
{
    /**
     * The expected data structure of the resource.
     *
     * @var array<string, mixed>
     */
    protected array $expectedStructure = [];

    /**
     * Get the resource class name being tested.
     *
     * @var class-string
     */
    protected $resource;

    /**
     * Get an example model instance for the resource.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $modelInstance;

    /**
     * Render the resource as an array.
     *
     * @return array<string, mixed>
     */
    protected function renderResource(): array
    {
        $resourceClass = $this->resource();
        $modelInstance = $this->modelInstance();

        /** @var \Illuminate\Http\Resources\Json\JsonResource $resource */
        $resource = new $resourceClass($modelInstance);

        /** @var array<string, mixed> $resourceable */
        $resourceable = $resource->toArray(App::make('request'));
        return $resourceable;
    }

    /**
     * Test the resource structure against the expected structure.
     *
     * @return void
     */
    public function test_resource_structure(): void
    {
        $resourceArray = $this->renderResource();

        foreach ($this->expectedStructure as $key => $type) {
            $this->assertArrayHasKey($key, $resourceArray, "Key '{$key}' is missing in the resource array");

            /** @var string $type */
            $this->assertIsType($type, $resourceArray[$key]);
        }

        $unexpectedKeys = array_diff(array_keys($resourceArray), array_keys($this->expectedStructure));
        $this->assertEmpty(
            $unexpectedKeys,
            'The resource contains unexpected keys: ' . implode(', ', $unexpectedKeys)
        );
    }

    /**
     * Assert that a value is of a given type.
     *
     * @param string $type
     * @param mixed $value
     * @return void
     */
    protected function assertIsType(string $type, mixed $value): void
    {
        match ($type) {
            'string' => $this->assertIsString($value),
            'int' => $this->assertIsInt($value),
            'bool' => $this->assertIsBool($value),
            'array' => $this->assertIsArray($value),
            'float' => $this->assertIsFloat($value),
            'null' => $this->assertNull($value),
            'object' => $this->assertIsObject($value),
            'resourceCollection' => $this->assertInstanceOf(AnonymousResourceCollection::class, $value),
            default => $this->fail("Unknown type: $type")
        };
    }
}
