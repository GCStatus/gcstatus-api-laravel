<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\BindCustomInterfacesToImplementations::class,
    App\Providers\BindRepositoryInterfaceServiceProvider::class,
    App\Providers\BindServiceInterfaceServiceProvider::class,
    App\Providers\HorizonServiceProvider::class,
    App\Providers\MissionStrategyServiceProvider::class,
    App\Providers\PulseServiceProvider::class,
    App\Providers\RewardStrategyServiceProvider::class,
    Illuminate\Broadcasting\BroadcastServiceProvider::class,
];
