<?php

namespace App\Jobs;

use App\Models\{User, Mission};
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Contracts\Services\MissionServiceInterface;

class GiveMissionRewardsJob implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * The awardable user.
     *
     * @var \App\Models\User
     */
    public User $user;

    /**
     * The rewardable mission.
     *
     * @var \App\Models\Mission
     */
    public Mission $mission;

    /**
     * The award service.
     *
     * @var \App\Contracts\Services\MissionServiceInterface
     */
    private MissionServiceInterface $missionService;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Mission $mission
     * @return void
     */
    public function __construct(
        User $user,
        Mission $mission,
    ) {
        $this->user = $user;
        $this->mission = $mission;
        $this->missionService = app(MissionServiceInterface::class);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->missionService->handleMissionCompletion($this->user, $this->mission);
    }
}
