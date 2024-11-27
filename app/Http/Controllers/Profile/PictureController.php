<?php

namespace App\Http\Controllers\Profile;

use App\Http\Resources\UserResource;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\PictureUpdateRequest;
use App\Contracts\Services\{
    AuthServiceInterface,
    ProfileServiceInterface,
};

class PictureController extends Controller
{
    /**
     * The auth service.
     *
     * @var \App\Contracts\Services\AuthServiceInterface
     */
    private AuthServiceInterface $authService;

    /**
     * The profile service.
     *
     * @var \App\Contracts\Services\ProfileServiceInterface
     */
    private ProfileServiceInterface $profileService;

    /**
     * Create a new class instance.
     *
     * @param \App\Contracts\Services\AuthServiceInterface $authService
     * @param \App\Contracts\Services\ProfileServiceInterface $profileService
     * @return void
     */
    public function __construct(
        AuthServiceInterface $authService,
        ProfileServiceInterface $profileService,
    ) {
        $this->authService = $authService;
        $this->profileService = $profileService;
    }

    /**
     * Handle the incoming request.
     *
     * @param \App\Http\Requests\Profile\PictureUpdateRequest $request
     * @return \App\Http\Resources\UserResource
     */
    public function __invoke(PictureUpdateRequest $request): UserResource
    {
        /** @var array<string, \Illuminate\Http\UploadedFile> $data */
        $data = $request->validated();

        /** @var \App\Models\User $user */
        $user = $this->authService->getAuthUser();

        $this->profileService->updatePicture($user, $data['file']);

        return UserResource::make($user->fresh('profile'));
    }
}
