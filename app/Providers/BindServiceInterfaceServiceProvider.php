<?php

namespace App\Providers;

class BindServiceInterfaceServiceProvider extends BaseInterfaceBindServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->bindServiceInterfacesToImplementations();
    }

    /**
     * Bind the service interfaces.
     *
     * @return void
     */
    private function bindServiceInterfacesToImplementations(): void
    {
        $this->bindInterfacesToImplementations(
            'Contracts/Services',
            'App\\Contracts\\Services',
            'App\\Services',
        );
    }
}
