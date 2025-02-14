<?php

namespace App\Services;

use App\DTO\SteamAppDTO;
use Illuminate\Database\Eloquent\Model;
use App\Contracts\Repositories\PublisherableRepositoryInterface;
use App\Contracts\Services\{
    PublisherServiceInterface,
    PublisherableServiceInterface,
};

class PublisherableService extends AbstractService implements PublisherableServiceInterface
{
    /**
     * The publisher service.
     *
     * @var \App\Contracts\Services\PublisherServiceInterface
     */
    private PublisherServiceInterface $publisherService;

    /**
     * Create a new service instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->publisherService = app(PublisherServiceInterface::class);
    }

    /**
     * The publisherable repository.
     *
     * @return \App\Contracts\Repositories\PublisherableRepositoryInterface
     */
    public function repository(): PublisherableRepositoryInterface
    {
        return app(PublisherableRepositoryInterface::class);
    }

    /**
     * Create the steam app publishers.
     *
     * @param \Illuminate\Database\Eloquent\Model $app
     * @param \App\DTO\SteamAppDTO $formattedApp
     * @return void
     */
    public function createPublishersForSteamApp(Model $app, SteamAppDTO $formattedApp): void
    {
        foreach ($formattedApp->publishers as $publisher) {
            /** @var \App\Models\Publisher $modelPublisher */
            $modelPublisher = $this->publisherService->firstOrCreate([
                'name' => $publisher,
            ]);

            $this->create([
                'publisherable_type' => $app::class,
                'publisherable_id' => $app->getKey(),
                'publisher_id' => $modelPublisher->id,
            ]);
        }
    }
}
