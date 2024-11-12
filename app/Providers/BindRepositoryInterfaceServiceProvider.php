<?php

namespace App\Providers;

class BindRepositoryInterfaceServiceProvider extends BaseInterfaceBindServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->bindRepositoryInterfacesToImplementations();
    }

    /**
     * Bind the repository interfaces.
     *
     * @return void
     */
    private function bindRepositoryInterfacesToImplementations(): void
    {
        $this->bindInterfacesToImplementations(
            'Contracts/Repositories',
            'App\\Contracts\\Repositories',
            'App\\Repositories',
        );
    }
}
