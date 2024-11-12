<?php

namespace Tests\Feature\Http;

use Tests\TestCase;
use Database\Seeders\LevelSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class BaseIntegrationTesting extends TestCase
{
    use RefreshDatabase;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->seed([LevelSeeder::class]);
    }
}
