<?php

namespace App\Traits;

use RuntimeException;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

/**
 * Automatically generates a slug for an Eloquent model based on the specified attribute.
 *
 * @method void generateSlug()
 * @package App\Traits
 */
trait HasSlug
{
    /**
     * Boot the trait and attach a model event listener to generate the slug.
     *
     * @return void
     */
    protected static function bootHasSlug(): void
    {
        static::saving(function (Model $model) {
            /** @var static $model */
            $model->generateSlug();
        });
    }

    /**
     * Generate a slug for the model.
     *
     * @throws RuntimeException
     * @return void
     */
    protected function generateSlug(): void
    {
        if (empty($this->sluggable)) {
            throw new RuntimeException('The sluggable property must be defined in the model.');
        }

        $attribute = $this->sluggable;

        if (!$this->getAttribute($attribute)) {
            throw new RuntimeException("The sluggable attribute '{$attribute}' does not exist on the model.");
        }

        $this->slug = $this->generateUniqueSlug($this->{$attribute});
    }

    /**
     * Generate a unique slug for the given value.
     *
     * @param string $value
     * @return string
     */
    protected function generateUniqueSlug(string $value): string
    {
        $counter = 1;
        $baseSlug = Str::slug($value);
        $uniqueSlug = $baseSlug;

        while (static::query()->where('slug', $uniqueSlug)->exists()) {
            $uniqueSlug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $uniqueSlug;
    }
}
