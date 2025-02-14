<?php

namespace App\Jobs\Steam;

use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Contracts\Services\SteamServiceInterface;

class CreateSteamAppByIdJob implements ShouldQueue
{
    use Queueable;

    /**
     * The steam app id.
     *
     * @var string
     */
    public string $appId;

    /**
     * Create a new job instance.
     *
     * @param string $appId
     * @return void
     */
    public function __construct(string $appId)
    {
        $this->appId = $appId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        app(SteamServiceInterface::class)->saveSteamApp($this->appId);
    }
}
