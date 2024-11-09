<?php

namespace Tests\Traits;

trait UnitModelHelpers
{
    /**
     * Test if the fillable attributes are correctly.
     *
     * @param array<int, string> $fillable
     * @return bool
     */
    public function assertHasFillables(array $fillable): void
    {
        /** @var string $model */
        $model = $this->model();

        /** @var \Illuminate\Database\Eloquent\Model $model */
        $model = new $model;

        $this->assertEquals($fillable, $model->getFillable());
    }

    /**
     * Test if the model uses the correctly traits.
     *
     * @param array<int, string-class> $traits
     * @return void
     */
    public function assertUsesTraits(array $traits): void
    {
        /** @var string $model */
        $model = $this->model();

        /** @var \Illuminate\Database\Eloquent\Model $model */
        $model = new $model;

        $modelTraits = array_keys(class_uses($model));

        $this->assertEquals($traits, $modelTraits);
    }

    /**
     * Test if the casts attributes are correctly.
     *
     * @param array<string, string> $casts
     * @return void
     */
    public function assertHasCasts(array $casts): void
    {
        /** @var string $model */
        $model = $this->model();

        /** @var \Illuminate\Database\Eloquent\Model $model */
        $model = new $model;

        $this->assertEquals($casts, $model->getCasts());
    }

    /**
     * Test if the interfaces attributes are correct.
     *
     * @param array<int, string-class> $interfaces
     * @return void
     */
    public function assertUsesInterfaces(array $interfaces): void
    {
        /** @var string $model */
        $model = $this->model();

        /** @var \Illuminate\Database\Eloquent\Model $model */
        $model = new $model;

        foreach ($interfaces as $interface) {
            $this->assertInstanceOf($interface, $model);
        }
    }
}
