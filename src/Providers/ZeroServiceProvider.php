<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Providers;

use Illuminate\Support\ServiceProvider;
use Mathrix\Lumen\Zero\Console\Commands\ProvidersCacheClearCommand;
use Mathrix\Lumen\Zero\Console\Commands\ProvidersCacheCommand;

class ZeroServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'zero');

        $this->commands([
            ProvidersCacheCommand::class,
            ProvidersCacheClearCommand::class,
        ]);
    }
}
