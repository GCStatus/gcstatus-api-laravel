<?php

namespace App\Services;

use League\Flysystem\Visibility;
use Illuminate\Http\UploadedFile;
use App\Contracts\Services\StorageServiceInterface;
use App\Contracts\Repositories\StorageRepositoryInterface;

class StorageService implements StorageServiceInterface
{
    /**
     * The storage repository.
     *
     * @var \App\Contracts\Repositories\StorageRepositoryInterface
     */
    private $storageRepository;

    /**
     * Create a new class instance.
     *
     * @param \App\Contracts\Repositories\StorageRepositoryInterface $storageRepository
     * @return void
     */
    public function __construct(StorageRepositoryInterface $storageRepository)
    {
        $this->storageRepository = $storageRepository;
    }

    /**
     * Create a file in storage.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $folder
     * @param string $visibility
     * @return string
     */
    public function create(UploadedFile $file, string $folder, string $visibility = Visibility::PRIVATE): string
    {
        return $this->storageRepository->create($file, $folder, $visibility);
    }

    /**
     * Check if file exists on storage.
     *
     * @param ?string $path
     * @return bool
     */
    public function exists(?string $path): bool
    {
        return $path ? $this->storageRepository->exists($path) : false;
    }

    /**
     * Create a file in storage with name.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $folder
     * @param string $name
     * @param string $visibility
     * @return string
     */
    public function createAs(
        UploadedFile $file,
        string $folder,
        string $name,
        string $visibility = Visibility::PRIVATE,
    ): string {
        return $this->storageRepository->createAs($file, $folder, $name, $visibility);
    }

    /**
     * Delete file from storage.
     *
     * @param string $path
     * @return void
     */
    public function delete(string $path): void
    {
        $this->storageRepository->delete($path);
    }

    /**
     * Get a file path from storage.
     *
     * @param ?string $path
     * @return ?string
     */
    public function getPath(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        return $this->storageRepository->getPath($path);
    }
}
