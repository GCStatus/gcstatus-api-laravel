<?php

namespace App\Services;

use App\DTO\SteamAppDTO;
use Illuminate\Database\Eloquent\Model;
use App\Contracts\Repositories\CategoriableRepositoryInterface;
use App\Contracts\Services\{
    CategoryServiceInterface,
    CategoriableServiceInterface,
};

class CategoriableService extends AbstractService implements CategoriableServiceInterface
{
    /**
     * The category service.
     *
     * @var \App\Contracts\Services\CategoryServiceInterface
     */
    private CategoryServiceInterface $categoryService;

    /**
     * Create a new service instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->categoryService = app(CategoryServiceInterface::class);
    }

    /**
     * The categoriable repository.
     *
     * @return \App\Contracts\Repositories\CategoriableRepositoryInterface
     */
    public function repository(): CategoriableRepositoryInterface
    {
        return app(CategoriableRepositoryInterface::class);
    }

    /**
     * Create the steam app categories.
     *
     * @param \Illuminate\Database\Eloquent\Model $app
     * @param \App\DTO\SteamAppDTO $formattedApp
     * @return void
     */
    public function createCategoriesForSteamApp(Model $app, SteamAppDTO $formattedApp): void
    {
        foreach ($formattedApp->categories as $category) {
            /** @var array<string, mixed> $category */
            /** @var \App\Models\Category $modelCategory */
            $modelCategory = $this->categoryService->firstOrCreate([
                'name' => $category['description'],
            ]);

            $this->create([
                'categoriable_type' => $app::class,
                'categoriable_id' => $app->getKey(),
                'category_id' => $modelCategory->id,
            ]);
        }
    }
}
