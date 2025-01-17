<?php

namespace Tests\Unit\Resources;

use Mockery;
use App\Models\{Commentable, User};
use App\Http\Resources\CommentableResource;
use Illuminate\Database\Eloquent\Collection;
use Tests\Contracts\Resources\BaseResourceTesting;

class CommentableResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for CommentableResource.
     *
     * @var array<string, string>
     */
    protected array $expectedStructure = [
        'id' => 'int',
        'comment' => 'string',
        'is_hearted' => 'bool',
        'hearts_count' => 'int',
        'user' => 'object',
        'replies' => 'resourceCollection',
    ];

    /**
     * Get the resource class being tested.
     *
     * @return class-string<CommentableResource>
     */
    public function resource(): string
    {
        return CommentableResource::class;
    }

    /**
     * Provide a mock instance of Commentable for testing.
     *
     * @return \App\Models\Commentable
     */
    public function modelInstance(): Commentable
    {
        $replies = Mockery::mock(Collection::class);
        $userMock = Mockery::mock(User::class)->makePartial();

        $commentableMock = Mockery::mock(Commentable::class)->makePartial();
        $commentableMock->shouldAllowMockingMethod('getAttribute');

        $commentableMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $commentableMock->shouldReceive('getAttribute')->with('comment')->andReturn(fake()->text());
        $commentableMock->shouldReceive('getAttribute')->with('is_hearted')->andReturn(fake()->boolean());
        $commentableMock->shouldReceive('getAttribute')->with('hearts_count')->andReturn(fake()->numberBetween(1, 999));

        $commentableMock->shouldReceive('getAttribute')->with('user')->andReturn($userMock);
        $commentableMock->shouldReceive('getAttribute')->with('replies')->andReturn($replies);

        /** @var \App\Models\Commentable $commentableMock */
        return $commentableMock;
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