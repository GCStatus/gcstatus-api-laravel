<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use Mockery\MockInterface;
use App\Services\StorageService;
use League\Flysystem\Visibility;
use Illuminate\Http\UploadedFile;
use App\Contracts\Services\StorageServiceInterface;
use App\Contracts\Repositories\StorageRepositoryInterface;

class StorageServiceTest extends TestCase
{
    /**
     * The storage service.
     *
     * @var \App\Contracts\Services\StorageServiceInterface
     */
    private StorageServiceInterface $storageService;

    /**
     * The mock storage repository.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $storageRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->storageRepository = Mockery::mock(StorageRepositoryInterface::class);

        /** @var \App\Contracts\Repositories\StorageRepositoryInterface $storageRepository */
        $storageRepository = $this->storageRepository;

        $this->storageService = new StorageService($storageRepository);
    }

    /**
     * Test if can create a new file on storage.
     *
     * @return void
     */
    public function test_if_can_create_a_new_file_on_storage(): void
    {
        $folder = 'fakes';
        $visibility = Visibility::PRIVATE;
        $file = UploadedFile::fake()->create('fake.png');

        $this->storageRepository
            ->shouldReceive('create')
            ->once()
            ->with($file, $folder, $visibility)
            ->andReturn('fakes/path_to_storage_file.png');

        $result = $this->storageService->create($file, $folder, $visibility);

        $this->assertEquals($result, "fakes/path_to_storage_file.png");
    }

    /**
     * Test if can check if a file exists on storage.
     *
     * @return void
     */
    public function test_if_can_check_if_a_file_exists_on_storage(): void
    {
        $path = 'fakes/path_to_storage_file.png';

        $this->storageRepository
            ->shouldReceive('exists')
            ->once()
            ->with($path)
            ->andReturnTrue();

        $result = $this->storageService->exists($path);

        $this->assertTrue($result);
    }

    /**
     * Test if can check if a file doesn't exist on storage by nullable path.
     *
     * @return void
     */
    public function test_if_can_check_if_a_file_doesnt_exist_on_storage_by_nullable_path(): void
    {
        $this->storageRepository->shouldNotReceive('exists');

        $result = $this->storageService->exists(null);

        $this->assertFalse($result);
    }

    /**
     * Test if can create a new file with alias on storage.
     *
     * @return void
     */
    public function test_if_can_create_a_new_file_with_alias_on_storage(): void
    {
        $folder = 'fakes';
        $alias = 'my-file';
        $visibility = Visibility::PRIVATE;
        $file = UploadedFile::fake()->create('fake.png');

        $this->storageRepository
            ->shouldReceive('createAs')
            ->once()
            ->with($file, $folder, $alias, $visibility)
            ->andReturn("fakes/$alias.png");

        $result = $this->storageService->createAs($file, $folder, $alias, $visibility);

        $this->assertEquals($result, "fakes/$alias.png");
    }

    /**
     * Test if can delete a file from storage.
     *
     * @return void
     */
    public function test_if_can_delete_a_file_from_storage(): void
    {
        $path = 'fakes/path_to_storage_file.png';

        $this->storageRepository
            ->shouldReceive('delete')
            ->once()
            ->with($path);

        $this->storageService->delete($path);

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations executed.');
    }

    /**
     * Test if can get the correct path from storage.
     *
     * @return void
     */
    public function test_if_can_get_the_correct_path_from_storage(): void
    {
        $path = 'fakes/path_to_storage_file.png';

        $this->storageRepository
            ->shouldReceive('getPath')
            ->once()
            ->with($path)
            ->andReturn($expected = "https://path.to.aws/$path");

        $result = $this->storageService->getPath($path);

        $this->assertEquals($result, $expected);
    }

    /**
     * Test if can get null for null paths.
     *
     * @return void
     */
    public function test_if_can_get_null_for_null_paths(): void
    {
        $this->storageRepository->shouldNotReceive('getPath');

        $result = $this->storageService->getPath(null);

        $this->assertEquals($result, null);
    }

    /**
     * Cleanup Mockery after each test.
     *
     * @return void
     */
    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
