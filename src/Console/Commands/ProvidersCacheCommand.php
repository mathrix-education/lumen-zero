<?php

namespace Mathrix\Lumen\Zero\Console\Commands;

use Brick\VarExporter\ExportException;
use Mathrix\Lumen\Zero\Providers\CacheableServiceProvider;
use Mathrix\Lumen\Zero\Providers\ObserverServiceProvider;
use Mathrix\Lumen\Zero\Providers\PolicyServiceProvider;
use Mathrix\Lumen\Zero\Providers\RegistrarServiceProvider;

/**
 * Class ProvidersCache.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 2.0.5
 */
class ProvidersCacheCommand extends BaseCommand
{
    protected $signature = "providers:cache";
    protected $description = "Manually trigger Service Providers cache.";


    /**
     * @throws ExportException
     */
    public function handle()
    {
        $this->cache(ObserverServiceProvider::class);
        $this->cache(PolicyServiceProvider::class);
        $this->cache(RegistrarServiceProvider::class);
    }


    /**
     * @param $serviceProviderClass
     *
     * @throws ExportException
     */
    public function cache($serviceProviderClass)
    {
        /** @var CacheableServiceProvider $serviceProvider */
        $serviceProvider = new $serviceProviderClass(app());
        $cacheFile = $serviceProvider->getCacheFile();

        if (!$serviceProvider->isCached()) {
            $serviceProvider->writeCache();
            $this->line("<comment>Written:</comment> $cacheFile");
        } else {
            $this->line("<comment>Ignored:</comment> $cacheFile");
        }
    }
}
