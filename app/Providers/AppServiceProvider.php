<?php

namespace App\Providers;

use Throwable;
use ReflectionClass;
use ReflectionMethod;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\{Auth, DB};
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Foundation\Application;
use App\Contracts\Services\CacheServiceInterface;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Auth\Passwords\{DatabaseTokenRepository, TokenRepositoryInterface};

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(TokenRepositoryInterface::class, function (Application $app) {
            /** @var string $appKey */
            $appKey = config('app.key');

            return new DatabaseTokenRepository(
                DB::connection(),
                $app->make(Hasher::class),
                'password_reset_tokens',
                $appKey,
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->configureCommands();
        $this->configurePasswordResetUrl();
        $this->instantiateCacheEloquentUser();
        $this->configureModelMacroForRelations();
    }

    /**
     * Configure the application's commands.
     *
     * @return void
     */
    private function configureCommands(): void
    {
        DB::prohibitDestructiveCommands(
            (bool)$this->app->environment('production'),
        );
    }

    /**
     * Bind the cache auth user on facade provider.
     *
     * @return void
     */
    private function instantiateCacheEloquentUser(): void
    {
        Auth::provider('cached-user', function (Application $app) {
            return new CachedAuthUserProvider(
                $app->make(Hasher::class),
                $app->make(CacheServiceInterface::class),
            );
        });
    }

    /**
     * Configure the url for the password reset.
     *
     * @return void
     */
    private function configurePasswordResetUrl(): void
    {
        ResetPassword::createUrlUsing(function (mixed $notifiable, string $token) {
            /** @var string $baseUrl */
            $baseUrl = config('gcstatus.front_base_url');

            /** @var \App\Models\User $notifiable */
            return "{$baseUrl}password/reset/{$token}/?email={$notifiable->getEmailForPasswordReset()}";
        });
    }

    /**
     * Create a macro for a new attribute on models to get relations.
     *
     * @return void
     */
    public function configureModelMacroForRelations(): void
    {
        Builder::macro('getModelRelationships', function () {
            /** @var \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model> $builder */
            $builder = $this;

            $model = $builder->getModel();

            /** @var array<string, class-string> $relationships */
            $relationships = [];
            $reflection = new ReflectionClass($model);

            foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                if ($method->class !== get_class($model)) {
                    continue;
                }

                if ($method->getNumberOfParameters() > 0) {
                    continue;
                }

                try {
                    $result = $method->invoke($model);
                    if ($result instanceof Relation) {
                        $relationships[$method->name] = get_class($result);
                    }
                } catch (Throwable $e) {
                    continue;
                }
            }

            return $relationships;
        });
    }
}
