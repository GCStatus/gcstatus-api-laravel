<?php

namespace App\Providers;

use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;

abstract class BaseInterfaceBindServiceProvider extends ServiceProvider
{
    /**
     * Bind interfaces to implementations.
     *
     * @param string $contractPath
     * @param string $namespace
     * @param string $implementationPathPrefix
     * @return void
     */
    protected function bindInterfacesToImplementations(
        string $contractPath,
        string $namespace,
        string $implementationPathPrefix
    ): void {
        $contractsPath = app_path($contractPath);

        /** @var array<int, string> $files */
        $files = scandir($contractsPath);

        foreach ($files as $file) {
            if (!Str::endsWith($file, 'Interface.php')) {
                continue;
            }

            $interface = "$namespace\\" . Str::replaceLast('.php', '', $file);

            $implementationClassName = Str::replaceLast('Interface', '', Str::replaceLast('.php', '', $file));
            $implementation = "$implementationPathPrefix\\$implementationClassName";

            if (class_exists($implementation)) {
                $this->app->bind($interface, $implementation);
            }
        }
    }
}
