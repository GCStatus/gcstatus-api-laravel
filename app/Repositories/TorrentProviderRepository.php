<?php

namespace App\Repositories;

use App\Models\TorrentProvider;
use App\Contracts\Repositories\TorrentProviderRepositoryInterface;

class TorrentProviderRepository extends AbstractRepository implements TorrentProviderRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function model(): TorrentProvider
    {
        return new TorrentProvider();
    }
}
