<?php

namespace App\Jobs;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Contracts\Services\UserMissionProgressServiceInterface;
use App\Models\{
    User,
    Mission,
    MissionRequirement,
};

class CompleteMissionJob implements ShouldQueue
{
    use Queueable;
    use Dispatchable;
    use SerializesModels;

    /**
     * The related user.
     *
     * @var \App\Models\User
     */
    public User $user;

    /**
     * The related mission.
     *
     * @var \App\Models\Mission
     */
    public Mission $mission;

    /**
     * The user mission progress service.
     *
     * @var \App\Contracts\Services\UserMissionProgressServiceInterface
     */
    public UserMissionProgressServiceInterface $userMissionProgressService;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Mission $mission
     * @return void
     */
    public function __construct(User $user, Mission $mission)
    {
        $this->user = $user;
        $this->mission = $mission;
        $this->userMissionProgressService = app(UserMissionProgressServiceInterface::class);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->mission->load('requirements');

        $this->mission->requirements->each(function (MissionRequirement $missionRequirement) {
            $this->userMissionProgressService->updateProgress($this->user, $missionRequirement);
        });

        GiveMissionRewardsJob::dispatchSync($this->user, $this->mission);
    }
}
