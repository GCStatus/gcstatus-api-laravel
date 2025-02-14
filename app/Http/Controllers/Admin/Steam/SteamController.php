<?php

namespace App\Http\Controllers\Admin\Steam;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Jobs\Steam\CreateSteamAppByIdJob;
use App\Contracts\Responses\ApiResponseInterface;
use App\Http\Requests\Admin\Steam\SteamAppStoreRequest;

class SteamController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \App\Http\Requests\Admin\Steam\SteamAppStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(
        SteamAppStoreRequest $request,
        ApiResponseInterface $response,
    ): JsonResponse {
        $data = $request->validated();

        /** @var string $appId */
        $appId = $data['app_id'];

        CreateSteamAppByIdJob::dispatch($appId);

        return response()->json(
            $response->setMessage('Steam App successfully added to queue and is running on background.')->toMessage(),
        );
    }
}
