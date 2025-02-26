<?php

namespace App\Services;

use App\DTO\SteamAppDTO;
use App\Models\Galleriable;
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
     * Create a new galleriable.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\Galleriable
     */
    public function create(array $data): Galleriable
    {
        /** @var \Illuminate\Http\UploadedFile $file */
        $file = issetGetter($data, 'file');

        $data['path'] = $data['s3'] ? storage()->create($file, 'games') : $data['url'];

        /** @var \App\Models\Galleriable */
        return $this->repository()->create($data);
    }

    /**
     * Delete the galleriable.
     *
     * @param mixed $id
     * @return void
     */
    public function delete(mixed $id): void
    {
        /** @var \App\Models\Galleriable $galleriable */
        $galleriable = $this->repository()->findOrFail($id);

        if ($galleriable->s3) {
            storage()->delete($galleriable->path);
        }

        $galleriable->delete();
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
            $this->repository()->create([
                'path' => $gallery['path'],
                'galleriable_type' => $app::class,
                'galleriable_id' => $app->getKey(),
                'media_type_id' => $gallery['type'],
            ]);
        }
    }
}
