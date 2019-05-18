<?php

namespace Mathrix\Lumen\Zero\Console\Commands;

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
class ProvidersCacheClearCommand extends BaseCommand
{
    protected $signature = "providers:cache:clear";
    protected $description = "Clear Service Providers cache.";


    public function handle()
    {
        $this->clear(ObserverServiceProvider::class);
        $this->clear(PolicyServiceProvider::class);
        $this->clear(RegistrarServiceProvider::class);
    }


    public function clear($serviceProviderClass)
    {
        /** @var CacheableServiceProvider $serviceProvider */
        $serviceProvider = new $serviceProviderClass(app());
        $cacheFile = $serviceProvider->getCacheFile();

        if ($serviceProvider->isCached()) {
            unlink($cacheFile);
            $this->line("<comment>Deleted:</comment> $cacheFile");
        } else {
            $this->line("<comment>Ignored:</comment> $cacheFile");
        }
    }
}
