<?php

namespace Tests\Unit\Traits;

use Mockery;
use Tests\TestCase;
use RuntimeException;
use App\Models\Store;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

class HasSlugTest extends TestCase
{
    /**
     * Test if can generate slug successfully.
     *
     * @return void
     */
    public function test_if_can_generate_slug_successfully(): void
    {
        $builder = Mockery::mock(Builder::class);

        $model = Mockery::mock(Store::class)->makePartial();
        $model->shouldReceive('getAttribute')->with('name')->andReturn(fake()->word());

        /** @phpstan-ignore-next-line */
        $expectedSlug = Str::slug($model->name);

        $builder->shouldReceive('where')
            ->once()
            ->with('slug', $expectedSlug)
            ->andReturnSelf();

        $builder->shouldReceive('exists')
            ->once()
            ->andReturnFalse();

        $model->shouldReceive('query')->andReturn($builder);

        /** @var \App\Models\Store $model */
        $model->generateSlug();

        $this->assertEquals($expectedSlug, $model->slug);
    }

    /**
     * Test if can generate unique slugs.
     *
     * @return void
     */
    public function test_if_can_generate_unique_slugs(): void
    {
        $builder = Mockery::mock(Builder::class);

        $model = Mockery::mock(Store::class)->makePartial();
        $model->shouldReceive('getAttribute')->with('name')->andReturn(fake()->word());

        /** @phpstan-ignore-next-line */
        $expectedSlug = Str::slug($model->name);

        $builder->shouldReceive('where')
            ->once()
            ->with('slug', $expectedSlug)
            ->andReturnSelf();

        $builder->shouldReceive('exists')
            ->once()
            ->andReturnTrue();

        $builder->shouldReceive('where')
            ->once()
            ->with('slug', $expectedSlug . '-1')
            ->andReturnSelf();

        $builder->shouldReceive('exists')
            ->once()
            ->andReturnFalse();

        $model->shouldReceive('query')->andReturn($builder);

        /** @var \App\Models\Store $model */
        $model->generateSlug();

        $expectedResult = $expectedSlug . '-1';

        $this->assertEquals($expectedResult, $model->slug);
    }

    /**
     * Test if missing sluggable property throws an exception.
     *
     * @return void
     */
    public function test_if_missing_sluggable_property_throws_an_exception(): void
    {
        $model = new class () extends Store {
            /**
             * The sluggable attribute.
             *
             * @var string
             */
            protected $sluggable = null;
        };

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The sluggable property must be defined in the model.');

        $model->save();
    }

    /**
     * Test if missing sluggable attribute throws an exception.
     *
     * @return void
     */
    public function test_if_missing_sluggable_attribute_throws_an_exception(): void
    {
        $model = new class () extends Store {
            /**
             * The sluggable attribute.
             *
             * @var string
             */
            protected $sluggable = 'nonExistentAttribute';
        };

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("The sluggable attribute 'nonExistentAttribute' does not exist on the model.");

        $model->save();
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
