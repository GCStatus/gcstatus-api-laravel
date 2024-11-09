<?php

namespace Tests\Contracts\Middlewares;

use Tests\TestCase;
use Illuminate\Http\{Request, Response};

abstract class BaseMiddlewareTesting extends TestCase implements ShouldTestMiddlewares
{
    /**
     * The middleware.
     *
     * @var object
     */
    protected $middleware;

    /**
     * The dummy request.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * The closure next.
     *
     * @var \Closure
     */
    protected $next;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $middleware = $this->middleware();

        $this->middleware = new $middleware();

        $this->request = Request::create('/', 'GET');

        $this->next = function () {
            return new Response("Next middleware");
        };
    }
}
