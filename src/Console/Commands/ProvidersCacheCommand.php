<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Console\Commands;

use Brick\VarExporter\ExportException;
use Mathrix\Lumen\Zero\Providers\CacheableServiceProvider;
use Mathrix\Lumen\Zero\Providers\ObserverServiceProvider;
use Mathrix\Lumen\Zero\Providers\PolicyServiceProvider;
use Mathrix\Lumen\Zero\Providers\RegistrarServiceProvider;

class ProvidersCacheCommand extends BaseCommand
{
    protected $signature   = 'providers:cache {--f|force : Force cache refresh}';
    protected $description = 'Manually trigger Service Providers cache.';

    /**
     * @throws ExportException
     */
    public function handle(): void
    {
        $this->cache(ObserverServiceProvider::class);
        $this->cache(PolicyServiceProvider::class);
        $this->cache(RegistrarServiceProvider::class);
    }

    /**
     * @param CacheableServiceProvider|string $serviceProviderClass The service provider class.
     *
     * @throws ExportException
     */
    public function cache($serviceProviderClass): void
    {
        $force = $this->option('force');

        /** @var CacheableServiceProvider $serviceProvider */
        $serviceProvider = new $serviceProviderClass($this->getLaravel());
        $cacheFile       = $serviceProvider->getCacheFile();

        if (!$serviceProvider->isCached() || $force) {
            $serviceProvider->writeCache();
            $this->line("<comment>Written:</comment> $cacheFile");
        } else {
            $this->line("<comment>Ignored:</comment> $cacheFile");
        }
    }
}
