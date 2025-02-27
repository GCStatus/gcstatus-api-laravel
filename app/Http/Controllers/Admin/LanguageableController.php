<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\LanguageableResource;
use App\Contracts\Services\LanguageableServiceInterface;
use Illuminate\Routing\Controllers\{Middleware, HasMiddleware};
use App\Http\Requests\Admin\Languageable\{LanguageableStoreRequest, LanguageableUpdateRequest};

class LanguageableController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     *
     * @return array<int, mixed>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('scopes:create:languageables', only: ['store']),
            new Middleware('scopes:update:languageables', only: ['update']),
            new Middleware('scopes:delete:languageables', only: ['destroy']),
        ];
    }

    /**
     * The languageable service.
     *
     * @var \App\Contracts\Services\LanguageableServiceInterface
     */
    private LanguageableServiceInterface $languageableService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Contracts\Services\LanguageableServiceInterface $languageableService
     * @return void
     */
    public function __construct(LanguageableServiceInterface $languageableService)
    {
        $this->languageableService = $languageableService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Admin\Languageable\LanguageableStoreRequest $request
     * @return \App\Http\Resources\Admin\LanguageableResource
     */
    public function store(LanguageableStoreRequest $request): LanguageableResource
    {
        try {
            /** @var array<string, mixed> $data */
            $data = $request->validated();

            $languageable = $this->languageableService->create($data);

            return LanguageableResource::make($languageable);
        } catch (Exception $e) {
            $this->handleDefaultExceptionLog('Failed to create a new languageable.', $e);

            throw $e;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Admin\Languageable\LanguageableUpdateRequest $request
     * @param mixed $id
     * @return \App\Http\Resources\Admin\LanguageableResource
     */
    public function update(LanguageableUpdateRequest $request, mixed $id): LanguageableResource
    {
        try {
            /** @var array<string, mixed> $data */
            $data = $request->validated();

            $languageable = $this->languageableService->update($data, $id);

            return LanguageableResource::make($languageable);
        } catch (Exception $e) {
            $this->handleDefaultExceptionLog('Failed to update a languageable.', $e);

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
        $this->languageableService->delete($id);
    }
}
