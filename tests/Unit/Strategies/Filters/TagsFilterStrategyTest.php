<?php

namespace Tests\Unit\Strategies\Filters;

use Mockery;
use PHPUnit\Framework\TestCase;
use Illuminate\Database\Eloquent\Builder;
use App\Strategies\Filters\TagsFilterStrategy;

class TagsFilterStrategyTest extends TestCase
{
    /**
     * Test if apply can adds the where has query to builder instance.
     *
     * @return void
     */
    public function test_apply_adds_where_has_query(): void
    {
        $slug = 'test-tag';

        $builderMock = Mockery::mock(Builder::class);

        $builderMock->shouldReceive('whereHas')
            ->once()
            ->with(
                'tags',
                $this->callback(function (callable $closure) use ($slug) {
                    $nestedBuilderMock = Mockery::mock(Builder::class);

                    $nestedBuilderMock
                        ->shouldReceive('where')
                        ->once()
                        ->with('slug', $slug);

                    $closure($nestedBuilderMock);

                    return true;
                })
            )->andReturnSelf();

        $strategy = new TagsFilterStrategy();

        /** @var \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model> $builderMock */
        $result = $strategy->apply($builderMock, $slug);

        $this->assertSame($builderMock, $result);
    }

    /**
     * Tear down application tests.
     *
     * @return void
     */
    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
