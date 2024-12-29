<?php

namespace App\Repositories;

use App\Models\UserTitle;
use Illuminate\Support\Facades\DB;
use App\Contracts\Repositories\UserTitleRepositoryInterface;

class UserTitleRepository extends AbstractRepository implements UserTitleRepositoryInterface
{
    /**
     * The user title repository.
     *
     * @return \App\Models\UserTitle
     */
    public function model(): UserTitle
    {
        return new UserTitle();
    }

    /**
     * @inheritDoc
     */
    public function toggleTitle(int $userId, mixed $titleId): void
    {
        /** @var string $titleId */
        $this->model()
            ->query()
            ->where('user_id', $userId)
            ->update([
                'enabled' => DB::raw("CASE WHEN title_id = $titleId THEN NOT enabled ELSE false END"),
            ]);
    }
}
