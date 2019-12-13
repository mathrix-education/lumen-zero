<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Providers;

use Illuminate\Support\ServiceProvider;
use Mathrix\Lumen\Zero\Console\Commands\ProvidersCacheCommand;

class ZeroServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/zero.php', 'zero');
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'zero');

        // Register Lumen Zero service providers
        $this->app->register(ObserverServiceProvider::class);
        $this->app->register(PolicyServiceProvider::class);
        $this->app->register(RegistrarServiceProvider::class);

        $this->commands([
            ProvidersCacheCommand::class,
        ]);
    }
}
