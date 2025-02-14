<?php

namespace App\Services;

use App\Models\Store;
use App\DTO\SteamAppDTO;
use Illuminate\Database\Eloquent\Model;
use App\Contracts\Repositories\StoreableRepositoryInterface;
use App\Contracts\Services\{
    StoreServiceInterface,
    StoreableServiceInterface,
};

class StoreableService extends AbstractService implements StoreableServiceInterface
{
    /**
     * The store service.
     *
     * @var \App\Contracts\Services\StoreServiceInterface
     */
    private StoreServiceInterface $storeService;

    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->storeService = app(StoreServiceInterface::class);
    }

    /**
     * The storeable repository.
     *
     * @return \App\Contracts\Repositories\StoreableRepositoryInterface
     */
    public function repository(): StoreableRepositoryInterface
    {
        return app(StoreableRepositoryInterface::class);
    }

    /**
     * Create the storeable with the price for the steam app.
     *
     * @param \Illuminate\Database\Eloquent\Model $app
     * @param \App\DTO\SteamAppDTO $formattedApp
     * @return void
     */
    public function createStoreableForSteamApp(Model $app, SteamAppDTO $formattedApp): void
    {
        /** @var \App\Models\Store $store */
        $store = $this->storeService->findOrFail(Store::STEAM_STORE_ID);

        $this->create([
            'store_id' => $store->id,
            'price' => $formattedApp->price,
            'storeable_type' => $app::class,
            'storeable_id' => $app->getKey(),
            'store_item_id' => $formattedApp->appId,
            'url' => sprintf('https://store.steampowered.com/app/%s', $formattedApp->appId),
        ]);
    }
}
