<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Repositories\TorrentProviderRepository;
use App\Contracts\Services\TorrentProviderServiceInterface;
use App\Contracts\Repositories\TorrentProviderRepositoryInterface;

class TorrentProviderServiceTest extends TestCase
{
    /**
     * Test if TorrentProviderService uses the TorrentProvider repository correctly.
     *
     * @return void
     */
    public function test_TorrentProviderService_repository_uses_TorrentProvider_repository(): void
    {
        $this->app->instance(TorrentProviderRepositoryInterface::class, new TorrentProviderRepository());

        /** @var \App\Services\TorrentProviderService $torrentProviderService */
        $torrentProviderService = app(TorrentProviderServiceInterface::class);

        $this->assertInstanceOf(TorrentProviderRepository::class, $torrentProviderService->repository());
    }
}
