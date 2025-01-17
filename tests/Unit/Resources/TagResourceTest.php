<?php

namespace Tests\Unit\Resources;

use Mockery;
use App\Models\Tag;
use App\Http\Resources\TagResource;
use Tests\Contracts\Resources\BaseResourceTesting;

class TagResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for TagResource.
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
     * @return class-string<TagResource>
     */
    public function resource(): string
    {
        return TagResource::class;
    }

    /**
     * Provide a mock instance of Tag for testing.
     *
     * @return \App\Models\Tag
     */
    public function modelInstance(): Tag
    {
        $tagMock = Mockery::mock(Tag::class)->makePartial();
        $tagMock->shouldAllowMockingMethod('getAttribute');

        $tagMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $tagMock->shouldReceive('getAttribute')->with('name')->andReturn(fake()->name());
        $tagMock->shouldReceive('getAttribute')->with('slug')->andReturn(fake()->name());

        /** @var \App\Models\Tag $tagMock */
        return $tagMock;
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
