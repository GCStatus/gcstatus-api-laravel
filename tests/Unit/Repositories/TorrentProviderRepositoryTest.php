<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\TorrentProvider;
use App\Contracts\Repositories\TorrentProviderRepositoryInterface;

class TorrentProviderRepositoryTest extends TestCase
{
    /**
     * The TorrentProvider repository.
     *
     * @var \App\Contracts\Repositories\TorrentProviderRepositoryInterface
     */
    private TorrentProviderRepositoryInterface $torrentProviderRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->torrentProviderRepository = app(TorrentProviderRepositoryInterface::class);
    }

    /**
     * Test if TorrentProviderRepository uses the TorrentProvider model correctly.
     *
     * @return void
     */
    public function test_TorrentProvider_repository_uses_TorrentProvider_model(): void
    {
        /** @var \App\Repositories\TorrentProviderRepository $torrentProviderRepository */
        $torrentProviderRepository = $this->torrentProviderRepository;

        $this->assertInstanceOf(TorrentProvider::class, $torrentProviderRepository->model());
    }
}
