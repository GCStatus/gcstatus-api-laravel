<?php

namespace App\Services;

use App\DTO\SteamAppDTO;
use Illuminate\Database\Eloquent\Model;
use App\Contracts\Repositories\DeveloperableRepositoryInterface;
use App\Contracts\Services\{
    DeveloperServiceInterface,
    DeveloperableServiceInterface,
};

class DeveloperableService extends AbstractService implements DeveloperableServiceInterface
{
    /**
     * The developer service.
     *
     * @var \App\Contracts\Services\DeveloperServiceInterface
     */
    private DeveloperServiceInterface $developerService;

    /**
     * Create a new service instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->developerService = app(DeveloperServiceInterface::class);
    }

    /**
     * The developerable repository.
     *
     * @return \App\Contracts\Repositories\DeveloperableRepositoryInterface
     */
    public function repository(): DeveloperableRepositoryInterface
    {
        return app(DeveloperableRepositoryInterface::class);
    }

    /**
     * Create the steam app developers.
     *
     * @param \Illuminate\Database\Eloquent\Model $app
     * @param \App\DTO\SteamAppDTO $formattedApp
     * @return void
     */
    public function createDevelopersForSteamApp(Model $app, SteamAppDTO $formattedApp): void
    {
        foreach ($formattedApp->developers as $developer) {
            /** @var \App\Models\Developer $modelDeveloper */
            $modelDeveloper = $this->developerService->firstOrCreate([
                'name' => $developer,
            ]);

            $this->create([
                'developerable_type' => $app::class,
                'developerable_id' => $app->getKey(),
                'developer_id' => $modelDeveloper->id,
            ]);
        }
    }
}
