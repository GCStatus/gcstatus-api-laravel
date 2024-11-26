<?php

namespace App\Repositories;

use Illuminate\Support\Carbon;
use League\Flysystem\Visibility;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Contracts\Repositories\StorageRepositoryInterface;

class StorageRepository implements StorageRepositoryInterface
{
    /**
     * Time of a file disponibility in hours.
     *
     * @var int
     */
    public const STORAGE_FILE_TTL = 2;

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
        return (string)Storage::put($folder, $file, $visibility);
    }

    /**
     * Check if file exists on storage.
     *
     * @param string $path
     * @return bool
     */
    public function exists(string $path): bool
    {
        return Storage::exists($path);
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
        return (string)Storage::putFileAs($folder, $file, $name, $visibility);
    }

    /**
     * Delete file from storage.
     *
     * @param string $path
     * @return void
     */
    public function delete(string $path): void
    {
        Storage::delete($path);
    }

    /**
     * Get a file path from storage.
     *
     * @param string $path
     * @return string
     */
    public function getPath(string $path): string
    {
        return Storage::temporaryUrl($path, Carbon::now()->addHours(self::STORAGE_FILE_TTL));
    }
}
