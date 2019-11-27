<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Providers;

use Illuminate\Support\ServiceProvider;

class ZeroServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'zero');
    }
}
