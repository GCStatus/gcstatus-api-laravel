<?php

namespace Tests\Unit\Resources;

use Mockery;
use App\Models\Genre;
use App\Http\Resources\GenreResource;
use Tests\Contracts\Resources\BaseResourceTesting;

class GenreResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for GenreResource.
     *
     * @var array<string, string>
     */
    protected array $expectedStructure = [
        'id' => 'int',
        'slug' => 'string',
        'name' => 'string',
    ];

    /**
     * Get the resource class being tested.
     *
     * @return class-string<GenreResource>
     */
    public function resource(): string
    {
        return GenreResource::class;
    }

    /**
     * Provide a mock instance of Genre for testing.
     *
     * @return \App\Models\Genre
     */
    public function modelInstance(): Genre
    {
        $genreMock = Mockery::mock(Genre::class)->makePartial();
        $genreMock->shouldAllowMockingMethod('getAttribute');

        $genreMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $genreMock->shouldReceive('getAttribute')->with('name')->andReturn(fake()->name());
        $genreMock->shouldReceive('getAttribute')->with('slug')->andReturn(fake()->name());

        /** @var \App\Models\Genre $genreMock */
        return $genreMock;
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
