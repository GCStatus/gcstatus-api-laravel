<?php

namespace Tests\Unit\Strategies\Filters;

use Mockery;
use PHPUnit\Framework\TestCase;
use Illuminate\Database\Eloquent\Builder;
use App\Strategies\Filters\CrackersFilterStrategy;

class CrackersFilterStrategyTest extends TestCase
{
    /**
     * Test if apply can adds the where has query to builder instance.
     *
     * @return void
     */
    public function test_apply_adds_where_has_query(): void
    {
        $slug = 'cracker-slug';

        $builderMock = Mockery::mock(Builder::class);

        $builderMock->shouldReceive('whereHas')
            ->once()
            ->with('crack', Mockery::on(function (callable $closure) use ($slug) {
                $nestedBuilderMock = Mockery::mock(Builder::class);

                $nestedBuilderMock->shouldReceive('whereHas')
                    ->once()
                    ->with('cracker', Mockery::on(function (callable $innerClosure) use ($slug) {
                        $innerMostBuilderMock = Mockery::mock(Builder::class);

                        $innerMostBuilderMock->shouldReceive('where')
                            ->once()
                            ->with('slug', $slug)
                            ->andReturnSelf();

                        $innerClosure($innerMostBuilderMock);

                        return true;
                    }))->andReturnSelf();

                $closure($nestedBuilderMock);

                return true;
            }))->andReturnSelf();

        $strategy = new CrackersFilterStrategy();

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
