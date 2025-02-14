<?php

namespace App\Services;

use App\Models\Game;
use App\DTO\SteamAppDTO;
use App\Contracts\Repositories\RequirementableRepositoryInterface;
use App\Contracts\Services\{
    RequirementableServiceInterface,
    RequirementTypeServiceInterface,
};

class RequirementableService extends AbstractService implements RequirementableServiceInterface
{
    /**
     * The requirement type service.
     *
     * @var \App\Contracts\Services\RequirementTypeServiceInterface
     */
    private RequirementTypeServiceInterface $requirementTypeService;

    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->requirementTypeService = app(RequirementTypeServiceInterface::class);
    }

    /**
     * The requirementable service.
     *
     * @return \App\Contracts\Repositories\RequirementableRepositoryInterface
     */
    public function repository(): RequirementableRepositoryInterface
    {
        return app(RequirementableRepositoryInterface::class);
    }

    /**
     * Create the requirements for the game.
     *
     * @param \App\Models\Game $game
     * @param \App\DTO\SteamAppDTO $formattedApp
     * @return void
     */
    public function createGameRequirements(Game $game, SteamAppDTO $formattedApp): void
    {
        foreach ($formattedApp->requirements as $requirement) {
            /** @var \App\Models\RequirementType $requirementType */
            $requirementType = $this->requirementTypeService->firstOrCreate([
                'os' => $requirement['os'],
                'potential' => $requirement['potential'],
            ]);

            $attributes = $requirement['attributes'];

            if (!empty($attributes)) {
                $this->create([
                    'network' => 'N/A',
                    'os' => $attributes['t_os'] ?? null,
                    'dx' => $attributes['dx'] ?? null,
                    'cpu' => $attributes['cpu'] ?? null,
                    'gpu' => $attributes['gpu'] ?? null,
                    'ram' => $attributes['ram'] ?? null,
                    'obs' => $attributes['obs'] ?? null,
                    'rom' => $attributes['storage'] ?? null,
                    'requirementable_id' => $game->id,
                    'requirementable_type' => $game::class,
                    'requirement_type_id' => $requirementType->id,
                ]);
            }
        }
    }
}
