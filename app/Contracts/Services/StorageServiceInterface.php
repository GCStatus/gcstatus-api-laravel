<?php

namespace App\Contracts\Services;

use League\Flysystem\Visibility;
use Illuminate\Http\UploadedFile;

interface StorageServiceInterface
{
    /**
     * Create a file in storage.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $folder
     * @param string $visibility
     * @return string
     */
    public function create(UploadedFile $file, string $folder, string $visibility = Visibility::PRIVATE): string;

    /**
     * Check if file exists on storage.
     *
     * @param ?string $path
     * @return bool
     */
    public function exists(?string $path): bool;

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
    ): string;

    /**
     * Delete file from storage.
     *
     * @param string $path
     * @return void
     */
    public function delete(string $path): void;

    /**
     * Get a file path from storage.
     *
     * @param ?string $path
     * @return ?string
     */
    public function getPath(?string $path): ?string;
}
