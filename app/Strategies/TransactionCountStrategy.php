<?php

namespace App\Strategies;

use App\Models\{User, MissionRequirement};
use App\Contracts\Strategies\MissionStrategyInterface;

class TransactionCountStrategy implements MissionStrategyInterface
{
    /**
     * @inheritDoc
     */
    public function calculateProgress(User $user, MissionRequirement $missionRequirement): int
    {
        $count = $user->transactions()->where('created_at', '>=', $missionRequirement->created_at)->count();

        return $count > $missionRequirement->goal ? $missionRequirement->goal : $count;
    }
}
