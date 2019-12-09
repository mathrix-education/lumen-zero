<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Console\Commands;

use Mathrix\Lumen\Zero\Providers\CacheableServiceProvider;
use Mathrix\Lumen\Zero\Providers\ObserverServiceProvider;
use Mathrix\Lumen\Zero\Providers\PolicyServiceProvider;
use Mathrix\Lumen\Zero\Providers\RegistrarServiceProvider;
use function app;
use function unlink;

class ProvidersCacheClearCommand extends BaseCommand
{
    protected $signature   = 'providers:cache:clear';
    protected $description = 'Clear Service Providers cache.';

    public function handle(): void
    {
        $this->clear(ObserverServiceProvider::class);
        $this->clear(PolicyServiceProvider::class);
        $this->clear(RegistrarServiceProvider::class);
    }

    public function clear(string $serviceProviderClass): void
    {
        /** @var CacheableServiceProvider $serviceProvider */
        $serviceProvider = new $serviceProviderClass(app());
        $cacheFile       = $serviceProvider->getCacheFile();

        if ($serviceProvider->isCached()) {
            unlink($cacheFile);
            $this->line("<comment>Deleted:</comment> $cacheFile");
        } else {
            $this->line("<comment>Ignored:</comment> $cacheFile");
        }
    }
}
