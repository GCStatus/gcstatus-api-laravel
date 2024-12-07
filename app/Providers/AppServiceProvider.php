<?php

namespace App\Providers;

use Throwable;
use ReflectionClass;
use ReflectionMethod;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\{App, Auth, DB};
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Foundation\Application;
use App\Contracts\Services\CacheServiceInterface;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Auth\Passwords\{DatabaseTokenRepository, TokenRepositoryInterface};

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
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
     */
    public function boot(): void
    {
        Auth::provider('cached-user', function (Application $app) {
            return new CachedAuthUserProvider(
                $app->make(Hasher::class),
                $app->make(CacheServiceInterface::class),
            );
        });

        ResetPassword::createUrlUsing(function (mixed $notifiable, string $token) {
            /** @var string $baseUrl */
            $baseUrl = config('gcstatus.front_base_url');

            /** @var \App\Models\User $notifiable */
            return "{$baseUrl}password/reset/{$token}/?email={$notifiable->getEmailForPasswordReset()}";
        });

        app('db')->listen(function ($query) {
            /** @var string $rule */
            $rule = '/\b(pulse|cache_locks|queue:restart)\b/';

            if (App::isLocal() && !(preg_match($rule, $query->sql) || preg_grep($rule, $query->bindings))) {
                app('log')->info('Database log', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time,
                ]);
            }
        });

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
