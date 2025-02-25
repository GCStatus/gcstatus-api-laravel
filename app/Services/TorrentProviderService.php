<?php

namespace App\Services;

use App\Contracts\Services\TorrentProviderServiceInterface;
use App\Contracts\Repositories\TorrentProviderRepositoryInterface;

class TorrentProviderService extends AbstractService implements TorrentProviderServiceInterface
{
    /**
     * @inheritDoc
     */
    public function repository(): TorrentProviderRepositoryInterface
    {
        return app(TorrentProviderRepositoryInterface::class);
    }
}
