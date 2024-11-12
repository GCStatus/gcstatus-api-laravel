<?php

namespace App\Providers;

use Throwable;
use ReflectionClass;
use ReflectionMethod;
use App\Exceptions\Handler;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->runningInConsole()) {
            $this->app->singleton(ExceptionHandler::class, Handler::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
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
