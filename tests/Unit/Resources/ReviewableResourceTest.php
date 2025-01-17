<?php

namespace Tests\Unit\Resources;

use Mockery;
use App\Models\{User, Reviewable};
use App\Http\Resources\ReviewableResource;
use Tests\Contracts\Resources\BaseResourceTesting;

class ReviewableResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for ReviewableResource.
     *
     * @var array<string, string>
     */
    protected array $expectedStructure = [
        'id' => 'int',
        'rate' => 'int',
        'review' => 'string',
        'consumed' => 'bool',
        'created_at' => 'string',
        'user' => 'object',
    ];

    /**
     * Get the resource class being tested.
     *
     * @return class-string<ReviewableResource>
     */
    public function resource(): string
    {
        return ReviewableResource::class;
    }

    /**
     * Provide a mock instance of Reviewable for testing.
     *
     * @return \App\Models\Reviewable
     */
    public function modelInstance(): Reviewable
    {
        $userMock = Mockery::mock(User::class)->makePartial();

        $reviewableMock = Mockery::mock(Reviewable::class)->makePartial();
        $reviewableMock->shouldAllowMockingMethod('getAttribute');

        $reviewableMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $reviewableMock->shouldReceive('getAttribute')->with('review')->andReturn(fake()->text());
        $reviewableMock->shouldReceive('getAttribute')->with('created_at')->andReturn(fake()->date());
        $reviewableMock->shouldReceive('getAttribute')->with('consumed')->andReturn(fake()->boolean());
        $reviewableMock->shouldReceive('getAttribute')->with('rate')->andReturn(fake()->numberBetween(1, 5));

        $reviewableMock->shouldReceive('getAttribute')->with('user')->andReturn($userMock);

        /** @var \App\Models\Reviewable $reviewableMock */
        return $reviewableMock;
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
