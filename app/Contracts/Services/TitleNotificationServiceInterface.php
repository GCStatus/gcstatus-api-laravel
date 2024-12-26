<?php

namespace App\Contracts\Services;

use App\Models\{User, Title};

interface TitleNotificationServiceInterface
{
    /**
     * Notify given user from new title.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Title $title
     * @return void
     */
    public function notifyNewTitle(User $user, Title $title): void;
}
