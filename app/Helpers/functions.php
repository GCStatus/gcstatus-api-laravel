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
