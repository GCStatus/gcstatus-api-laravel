<?php

namespace App\Jobs;

use Illuminate\Queue\SerializesModels;
use App\Models\{User, MissionRequirement};
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Contracts\Services\{
    AwardServiceInterface,
    MissionRequirementServiceInterface,
    UserMissionProgressServiceInterface,
};

class CalculateMissionProgressByKeyJob implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * The mission requirements key.
     *
     * @var string
     */
    public string $key;

    /**
     * The related user.
     *
     * @var \App\Models\User
     */
    public User $user;

    /**
     * The award service.
     *
     * @var \App\Contracts\Services\AwardServiceInterface
     */
    public AwardServiceInterface $awardService;

    /**
     * The mission requirements service.
     *
     * @var \App\Contracts\Services\MissionRequirementServiceInterface
     */
    public MissionRequirementServiceInterface $missionRequirementService;

    /**
     * The user mission progress service.
     *
     * @var \App\Contracts\Services\UserMissionProgressServiceInterface
     */
    public UserMissionProgressServiceInterface $userMissionProgressService;

    /**
     * Create a new job instance.
     *
     * @param string $key
     * @param \App\Models\User $user
     * @return void
     */
    public function __construct(string $key, User $user)
    {
        $this->key = $key;
        $this->user = $user;
        $this->awardService = app(AwardServiceInterface::class);
        $this->missionRequirementService = app(MissionRequirementServiceInterface::class);
        $this->userMissionProgressService = app(UserMissionProgressServiceInterface::class);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $requirements = $this->missionRequirementService->findByKey($this->key);

        $requirements->each(function (MissionRequirement $missionRequirement) {
            $this->userMissionProgressService->updateProgress($this->user, $missionRequirement);

            /** @var \App\Models\Mission $mission */
            $mission = $missionRequirement->mission;

            if (progressCalculator()->isMissionComplete($this->user, $mission)) {
                GiveMissionRewardsJob::dispatch($this->user, $mission);
            }
        });
    }
}