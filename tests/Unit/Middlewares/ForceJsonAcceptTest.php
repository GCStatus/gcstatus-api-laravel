<?php

namespace Tests\Unit\Middlewares;

use App\Http\Middleware\ForceJsonAccept;
use Tests\Contracts\Middlewares\BaseMiddlewareTesting;

class ForceJsonAcceptTest extends BaseMiddlewareTesting
{
    /**
     * The force json accept middleware.
     *
     * @return string
     */
    public function middleware(): string
    {
        return ForceJsonAccept::class;
    }

    /**
     * Test if can put the accept application json in headers.
     *
     * @return void
     */
    public function test_if_can_put_the_accept_application_json_in_headers(): void
    {
        /** @var \App\Http\Middleware\ForceJsonAccept $middleware */
        $middleware = $this->middleware;

        $middleware->handle($this->request, $this->next);

        $this->assertEquals('application/json', $this->request->header('Accept'));
    }
}
