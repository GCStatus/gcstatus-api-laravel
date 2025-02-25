<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\LanguageResource;
use App\Contracts\Services\LanguageServiceInterface;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controllers\{Middleware, HasMiddleware};
use App\Http\Requests\Admin\Language\{LanguageStoreRequest, LanguageUpdateRequest};

class LanguageController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     *
     * @return array<int, mixed>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('scopes:view:languages'),
            new Middleware('scopes:create:languages', only: ['store']),
            new Middleware('scopes:update:languages', only: ['update']),
            new Middleware('scopes:delete:languages', only: ['destroy']),
        ];
    }

    /**
     * The language service.
     *
     * @var \App\Contracts\Services\LanguageServiceInterface
     */
    private LanguageServiceInterface $languageService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Contracts\Services\LanguageServiceInterface $languageService
     * @return void
     */
    public function __construct(LanguageServiceInterface $languageService)
    {
        $this->languageService = $languageService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return LanguageResource::collection(
            $this->languageService->all(),
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Admin\Language\LanguageStoreRequest $request
     * @return \App\Http\Resources\Admin\LanguageResource
     */
    public function store(LanguageStoreRequest $request): LanguageResource
    {
        try {
            /** @var array<string, mixed> $data */
            $data = $request->validated();

            $language = $this->languageService->create($data);

            return LanguageResource::make($language);
        } catch (Exception $e) {
            $this->handleDefaultExceptionLog('Failed to create a new language.', $e);

            throw $e;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Admin\Language\LanguageUpdateRequest $request
     * @param mixed $id
     * @return \App\Http\Resources\Admin\LanguageResource
     */
    public function update(LanguageUpdateRequest $request, mixed $id): LanguageResource
    {
        try {
            /** @var array<string, mixed> $data */
            $data = $request->validated();

            $language = $this->languageService->update($data, $id);

            return LanguageResource::make($language);
        } catch (Exception $e) {
            $this->handleDefaultExceptionLog('Failed to update a language.', $e);

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
        $this->languageService->delete($id);
    }
}
