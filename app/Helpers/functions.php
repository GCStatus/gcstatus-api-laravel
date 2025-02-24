<?php

if (!function_exists('normalizeMorphAdmin')) {
    /**
     * Normalize the model class name to admin pattern.
     *
     * @param class-string $class
     * @return string
     */
    function normalizeMorphAdmin(string $class): string
    {
        return 'App\\Models\\GCStatus\\' . class_basename($class);
    }
}

if (!function_exists('issetGetter')) {
    /**
     * Get the value from array if index exists.
     *
     * @param array<string, mixed> $data
     * @param string $attribute
     * @return mixed
     */
    function issetGetter(array $data, string $attribute): mixed
    {
        return isset($data[$attribute]) ? $data[$attribute] : null;
    }
}
