<?php

namespace Tests\Contracts\Middlewares;

interface ShouldTestMiddlewares
{
    /**
     * The contract to get middleware class-string.
     *
     * @return class-string
     */
    public function middleware(): string;
}
