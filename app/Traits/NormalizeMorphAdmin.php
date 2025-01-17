<?php

namespace App\Traits;

use RuntimeException;

/**
 * Automatically normalizes the morph type for an Eloquent model based on the specified attribute.
 *
 * @method void normalizeMorphType()
 * @package App\Traits
 */
trait NormalizeMorphAdmin
{
    /**
     * Boot the trait and attach a model event listener to normalize the morph type.
     *
     * @return void
     */
    protected static function bootNormalizeMorphAdmin(): void
    {
        static::saving(function ($model) {
            /** @var static $model */
            $model->normalizeMorphType();
        });
    }

    /**
     * Normalize the morph type for the model.
     *
     * @throws RuntimeException
     * @return void
     */
    protected function normalizeMorphType(): void
    {
        if (empty($this->morphableAttribute)) {
            throw new RuntimeException('The morphableAttribute property must be defined in the model.');
        }

        $attribute = $this->morphableAttribute;

        if (!$this->getAttribute($attribute)) {
            throw new RuntimeException("The morphable attribute '{$attribute}' does not exist on the model.");
        }

        $value = $this->{$attribute};

        if (strpos($value, 'GCStatus') === false) {
            $this->{$attribute} = 'App\\Models\\GCStatus\\' . class_basename($value);
        }
    }
}
