<?php

namespace Tests\Unit\Strategies\Filters;

use Mockery;
use PHPUnit\Framework\TestCase;
use Illuminate\Database\Eloquent\Builder;
use App\Strategies\Filters\CracksFilterStrategy;

class CracksFilterStrategyTest extends TestCase
{
    /**
     * Test if apply can adds the where has query to builder instance.
     *
     * @return void
     */
    public function test_apply_adds_where_has_query(): void
    {
        /** @var string $value */
        $value = fake()->randomElement(['cracked', 'cracked-oneday', 'uncracked']);

        $builderMock = Mockery::mock(Builder::class);

        $builderMock->shouldReceive('whereHas')
            ->once()
            ->with('crack', Mockery::on(function (callable $closure) use ($value) {
                $nestedBuilderMock = Mockery::mock(Builder::class);

                $nestedBuilderMock->shouldReceive('whereHas')
                    ->once()
                    ->with('status', Mockery::on(function (callable $innerClosure) use ($value) {
                        $innerMostBuilderMock = Mockery::mock(Builder::class);

                        if ($value === 'cracked') {
                            $innerMostBuilderMock->shouldReceive('whereIn')
                                ->once()
                                ->with('name', ['cracked', 'cracked-oneday'])
                                ->andReturnSelf();
                        } else {
                            $innerMostBuilderMock->shouldReceive('where')
                                ->once()
                                ->with('name', $value)
                                ->andReturnSelf();
                        }

                        $innerClosure($innerMostBuilderMock);

                        return true;
                    }))->andReturnSelf();

                $closure($nestedBuilderMock);

                return true;
            }))->andReturnSelf();

        $strategy = new CracksFilterStrategy();

        /** @var \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model> $builderMock */
        $result = $strategy->apply($builderMock, $value);

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
