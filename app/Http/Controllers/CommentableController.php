<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentableResource;
use App\Contracts\Services\CommentableServiceInterface;
use App\Http\Requests\Commentable\CommentableStoreRequest;

class CommentableController extends Controller
{
    /**
     * The commentable service.
     *
     * @var \App\Contracts\Services\CommentableServiceInterface
     */
    private CommentableServiceInterface $commentableService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Contracts\Services\CommentableServiceInterface $commentableService
     * @return void
     */
    public function __construct(CommentableServiceInterface $commentableService)
    {
        $this->commentableService = $commentableService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Commentable\CommentableStoreRequest $request
     * @return \App\Http\Resources\CommentableResource
     */
    public function store(CommentableStoreRequest $request): CommentableResource
    {
        /** @var array<string, mixed> $data */
        $data = $request->validated();

        $commentable = $this->commentableService->create($data);

        return CommentableResource::make(
            $commentable->load('user', 'children'),
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param mixed $id
     * @return void
     */
    public function destroy(mixed $id): void
    {
        $this->commentableService->delete($id);
    }
}
