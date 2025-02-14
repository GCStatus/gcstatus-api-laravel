<?php

namespace App\Services;

use App\DTO\SteamAppDTO;
use Illuminate\Database\Eloquent\Model;
use App\Contracts\Services\GalleriableServiceInterface;
use App\Contracts\Repositories\GalleriableRepositoryInterface;

class GalleriableService extends AbstractService implements GalleriableServiceInterface
{
    /**
     * The galleriable repository.
     *
     * @return \App\Contracts\Repositories\GalleriableRepositoryInterface
     */
    public function repository(): GalleriableRepositoryInterface
    {
        return app(GalleriableRepositoryInterface::class);
    }

    /**
     * Create the galleriables for steam service.
     *
     * @param \Illuminate\Database\Eloquent\Model $app
     * @param \App\DTO\SteamAppDTO $formattedApp
     * @return void
     */
    public function createGalleriablesForSteamApp(Model $app, SteamAppDTO $formattedApp): void
    {
        foreach ($formattedApp->galleries as $gallery) {
            /** @var array<string, mixed> $gallery */
            $this->create([
                'path' => $gallery['path'],
                'galleriable_type' => $app::class,
                'galleriable_id' => $app->getKey(),
                'media_type_id' => $gallery['type'],
            ]);
        }
    }
}
