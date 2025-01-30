<?php

namespace Tests\Unit\Strategies\Filters;

use Mockery;
use PHPUnit\Framework\TestCase;
use Illuminate\Database\Eloquent\Builder;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Strategies\Filters\CracksFilterStrategy;

class CracksFilterStrategyTest extends TestCase
{
    /**
     * Data provider for testing different filter values.
     *
     * @return array<int, mixed>
     */
    public static function filterValuesProvider(): array
    {
        return [
            ['cracked'],
            ['uncracked'],
            ['cracked-oneday'],
        ];
    }

    /**
     * Test if apply adds the correct where conditions based on the filter value.
     *
     * @param string $value
     * @return void
     */
    #[DataProvider('filterValuesProvider')]
    public function test_apply_adds_correct_where_conditions(string $value): void
    {
        $builderMock = Mockery::mock(Builder::class);

        if ($value === 'uncracked') {
            $builderMock->shouldReceive('where')->once()->with(Mockery::on(function (callable $queryClosure) {
                $nestedBuilderMock = Mockery::mock(Builder::class);

                $nestedBuilderMock->shouldReceive('whereDoesntHave')
                    ->once()
                    ->with('crack')
                    ->andReturnSelf();

                $nestedBuilderMock->shouldReceive('orWhereHas')
                    ->once()
                    ->with('crack', Mockery::on(function (callable $subClosure) {
                        $crackBuilderMock = Mockery::mock(Builder::class);
                        $crackBuilderMock->shouldReceive('whereDoesntHave')
                            ->once()
                            ->with('status', Mockery::on(function (callable $innerClosure) {
                                $statusBuilderMock = Mockery::mock(Builder::class);
                                $statusBuilderMock->shouldReceive('whereIn')
                                    ->once()
                                    ->with('name', ['cracked', 'cracked-oneday'])
                                    ->andReturnSelf();
                                $innerClosure($statusBuilderMock);
                                return true;
                            }))->andReturnSelf();
                        $subClosure($crackBuilderMock);
                        return true;
                    }))->andReturnSelf();

                $queryClosure($nestedBuilderMock);

                return true;
            }))->andReturnSelf();
        } else {
            $builderMock->shouldReceive('where')->once()->with(Mockery::on(function (callable $queryClosure) use ($value) {
                $nestedBuilderMock = Mockery::mock(Builder::class);

                $nestedBuilderMock->shouldReceive('whereHas')
                    ->once()
                    ->with('crack', Mockery::on(function (callable $closure) use ($value) {
                        $crackBuilderMock = Mockery::mock(Builder::class);
                        $crackBuilderMock->shouldReceive('whereHas')
                            ->once()
                            ->with('status', Mockery::on(function (callable $innerClosure) use ($value) {
                                $statusBuilderMock = Mockery::mock(Builder::class);
                                if ($value === 'cracked') {
                                    $statusBuilderMock->shouldReceive('whereIn')
                                        ->once()
                                        ->with('name', ['cracked', 'cracked-oneday'])
                                        ->andReturnSelf();
                                } else {
                                    $statusBuilderMock->shouldReceive('where')
                                        ->once()
                                        ->with('name', $value)
                                        ->andReturnSelf();
                                }
                                $innerClosure($statusBuilderMock);
                                return true;
                            }))->andReturnSelf();
                        $closure($crackBuilderMock);
                        return true;
                    }))->andReturnSelf();

                $queryClosure($nestedBuilderMock);
                return true;
            }))->andReturnSelf();
        }

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
