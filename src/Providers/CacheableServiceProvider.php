<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Providers;

use Brick\VarExporter\ExportException;
use Brick\VarExporter\VarExporter;
use Illuminate\Support\ServiceProvider;
use Mathrix\Lumen\Zero\Console\Commands\ProvidersCacheClearCommand;
use Mathrix\Lumen\Zero\Console\Commands\ProvidersCacheCommand;
use const LOCK_EX;
use function app;
use function dirname;
use function file_exists;
use function file_put_contents;
use function is_dir;
use function mkdir;

abstract class CacheableServiceProvider extends ServiceProvider
{
    public const CACHE_FILE            = null;
    public const CACHE_MODE_ALWAYS     = 0;
    public const CACHE_ON_DEMAND       = 1;
    public static $CacheMode           = self::CACHE_ON_DEMAND;

    /**
     * Boot the service provider.
     *
     * @throws ExportException
     */
    final public function boot()
    {
        if (!$this->isCached() && static::$CacheMode === self::CACHE_MODE_ALWAYS) {
            $this->writeCache();
        }

        if ($this->isCached()) {
            $this->apply($this->loadCache());
        } else {
            $data = $this->loadDynamic();
            $this->apply($data);
        }
    }

    /**
     * @return string The cache file real path.
     */
    public function getCacheFile(): string
    {
        return app()->basePath(static::CACHE_FILE); // phpcs:ignore
    }

    /**
     * @return bool If the cache file exists.
     */
    public function isCached(): bool
    {
        return file_exists($this->getCacheFile());
    }

    /**
     * @return mixed Load the cached data.
     */
    public function loadCache()
    {
        /** @noinspection PhpIncludeInspection */
        return require $this->getCacheFile();
    }

    /**
     * Write the cache file.
     *
     * @throws ExportException
     */
    public function writeCache()
    {
        $data = $this->loadDynamic();
        $code = VarExporter::export($data, VarExporter::ADD_RETURN);
        $code = "<?php\n$code";

        $cacheFilePath = $this->getCacheFile();
        $cacheFileDir  = dirname($cacheFilePath);

        if (!is_dir($cacheFileDir)) {
            mkdir($cacheFileDir, 0755, true);
        }

        file_put_contents($this->getCacheFile(), $code, LOCK_EX);
    }

    /**
     * @return mixed Dynamically load the data required by the service provider.
     */
    abstract public function loadDynamic();

    /**
     * @param mixed $data The data, from the cache or dynamically loaded.
     */
    abstract public function apply($data): void;
}
