<?php

namespace Tests\Feature\Http;

use Tests\TestCase;

class HealthTest extends TestCase
{
    /**
     * Test if application is healthy and ready to receive requests.
     *
     * @return void
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $this->getJson('/')->assertSee('Application up');
    }
}
