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
    public $appId;

    /**
     * The steam service.
     *
     * @var \App\Contracts\Services\SteamServiceInterface
     */
    private SteamServiceInterface $steamService;

    /**
     * Create a new job instance.
     *
     * @param string $appId
     * @return void
     */
    public function __construct(string $appId)
    {
        $this->appId = $appId;

        $this->steamService = app(SteamServiceInterface::class);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->steamService->saveSteamApp($this->appId);
    }
}
