<?php

namespace Tests\Unit\Traits;

use Mockery;
use Tests\TestCase;
use RuntimeException;
use App\Models\Heartable;

class NormalizeMorphAdminTest extends TestCase
{
    /**
     * Test if can generate the morph type attribute successfully.
     *
     * @return void
     */
    public function test_if_can_generate_the_morph_type_attribute_successfully(): void
    {
        $model = Mockery::mock(Heartable::class)->makePartial();
        $model->shouldReceive('getAttribute')->with('heartable_type')->andReturn('Game');
        $model->shouldReceive('setAttribute')->with('heartable_type', Mockery::type('string'))->andReturnUsing(function (string $attr, string $value) use ($model) {
            $model->{$attr} = $value;
        });

        $expectedMorph = 'App\\Models\\GCStatus\\Game';

        /** @var \App\Models\Heartable $model */
        $model->normalizeMorphType();

        $this->assertEquals($expectedMorph, $model->heartable_type);
    }

    /**
     * Test if missing morphable property throws an exception.
     *
     * @return void
     */
    public function test_if_missing_morphable_property_throws_an_exception(): void
    {
        $model = new class () extends Heartable {
            /**
             * The morphable attribute.
             *
             * @var string
             */
            protected $morphableAttribute = null;
        };

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The morphableAttribute property must be defined in the model.');

        $model->save();
    }

    /**
     * Test if missing morphable attribute throws an exception.
     *
     * @return void
     */
    public function test_if_missing_morphable_attribute_throws_an_exception(): void
    {
        $model = new class () extends Heartable {
            /**
             * The morphable attribute.
             *
             * @var string
             */
            protected $morphableAttribute = 'nonExistentAttribute';
        };

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("The morphable attribute 'nonExistentAttribute' does not exist on the model.");

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
