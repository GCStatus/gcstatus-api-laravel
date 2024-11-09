<?php

namespace Tests\Contracts\Middlewares;

interface ShouldTestMiddlewares
{
    /**
     * The contract to get middleware class-string.
     *
     * @return string-class
     */
    public function middleware(): string;
}
