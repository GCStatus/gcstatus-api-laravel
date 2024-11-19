<?php

namespace Tests\Feature\Http;

use Tests\TestCase;
use Tests\Traits\HasDummyUser;
use Database\Seeders\LevelSeeder;
use App\Contracts\Services\AuthServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class BaseIntegrationTesting extends TestCase
{
    use HasDummyUser;
    use RefreshDatabase;

    /**
     * The auth service.
     *
     * @var \App\Contracts\Services\AuthServiceInterface
     */
    protected AuthServiceInterface $authService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->seed([LevelSeeder::class]);
        $this->authService = app(AuthServiceInterface::class);
    }
}
