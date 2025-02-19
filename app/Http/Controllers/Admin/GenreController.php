<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\GenreResource;
use App\Contracts\Services\GenreServiceInterface;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controllers\{Middleware, HasMiddleware};
use App\Http\Requests\Admin\Genre\{GenreStoreRequest, GenreUpdateRequest};

class GenreController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     *
     * @return array<int, mixed>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('scopes:view:genres'),
            new Middleware('scopes:create:genres', only: ['store']),
            new Middleware('scopes:update:genres', only: ['update']),
            new Middleware('scopes:delete:genres', only: ['destroy']),
        ];
    }

    /**
     * The genre service.
     *
     * @var \App\Contracts\Services\GenreServiceInterface
     */
    private GenreServiceInterface $genreService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Contracts\Services\GenreServiceInterface $genreService
     * @return void
     */
    public function __construct(GenreServiceInterface $genreService)
    {
        $this->genreService = $genreService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return GenreResource::collection(
            $this->genreService->all(),
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Admin\Genre\GenreStoreRequest $request
     * @return \App\Http\Resources\Admin\GenreResource
     */
    public function store(GenreStoreRequest $request): GenreResource
    {
        try {
            /** @var array<string, mixed> $data */
            $data = $request->validated();

            $genre = $this->genreService->create($data);

            return GenreResource::make($genre);
        } catch (Exception $e) {
            $this->handleDefaultExceptionLog('Failed to create a new genre.', $e);

            throw $e;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Admin\Genre\GenreUpdateRequest $request
     * @param mixed $id
     * @return \App\Http\Resources\Admin\GenreResource
     */
    public function update(GenreUpdateRequest $request, mixed $id): GenreResource
    {
        try {
            /** @var array<string, mixed> $data */
            $data = $request->validated();

            $genre = $this->genreService->update($data, $id);

            return GenreResource::make($genre);
        } catch (Exception $e) {
            $this->handleDefaultExceptionLog('Failed to update a genre.', $e);

            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param mixed $id
     * @return void
     */
    public function destroy(mixed $id): void
    {
        $this->genreService->delete($id);
    }
}
