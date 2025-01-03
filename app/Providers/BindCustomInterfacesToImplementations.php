<?php

namespace App\Providers;

use InvalidArgumentException;
use Illuminate\Contracts\Foundation\Application;
use App\Services\Validation\{EmailValidator, NicknameValidator, SteamResponseValidator};
use App\Contracts\Services\Validation\{IdentifierValidatorInterface, SteamResponseValidatorInterface};

class BindCustomInterfacesToImplementations extends BaseInterfaceBindServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->bindClientInterfacesToImplementations();
        $this->bindResponseInterfacesToImplementations();

        $this->app->bind(IdentifierValidatorInterface::class, function (Application $app, array $params) {
            $identifierType = $params['type'] ?? null;

            return match ($identifierType) {
                'email' => $app->make(EmailValidator::class),
                'nickname' => $app->make(NicknameValidator::class),
                default => throw new InvalidArgumentException("Invalid identifier type: $identifierType", 400),
            };
        });
        $this->app->bind(SteamResponseValidatorInterface::class, SteamResponseValidator::class);
    }

    /**
     * Bind the responses interfaces.
     *
     * @return void
     */
    private function bindResponseInterfacesToImplementations(): void
    {
        $this->bindInterfacesToImplementations(
            'Contracts/Responses',
            'App\\Contracts\\Responses',
            'App\\Responses',
        );
    }

    /**
     * Bind the client interfaces.
     *
     * @return void
     */
    private function bindClientInterfacesToImplementations(): void
    {
        $this->bindInterfacesToImplementations(
            'Contracts/Clients',
            'App\\Contracts\\Clients',
            'App\\Clients',
        );
    }
}
