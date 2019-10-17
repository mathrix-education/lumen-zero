<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Providers;

use Exception;
use HaydenPierce\ClassFinder\ClassFinder;
use Illuminate\Support\Collection;
use Mathrix\Lumen\Zero\Models\BaseModel;
use Mathrix\Lumen\Zero\Utils\ClassResolver;
use function in_array;

/**
 * Provides all observers to the corresponding models.
 */
class ObserverServiceProvider extends CacheableServiceProvider
{
    public const CACHE_FILE = 'bootstrap/cache/observers.php';

    /** @var array Ignored observers */
    public static $IgnoredObservers = [];

    /**
     * @return array Dynamically load the model observers.
     *
     * @throws Exception
     */
    public function loadDynamic(): array
    {
        return Collection::make(ClassFinder::getClassesInNamespace(ClassResolver::$ObserversNamespace))
            ->reject(static function (string $observerClass) {
                return in_array($observerClass, self::$IgnoredObservers)
                    || ClassResolver::getModelClass($observerClass) === null;
            })
            ->mapWithKeys(static function (string $observerClass) {
                $modelClass = ClassResolver::getModelClass($observerClass);

                return [$modelClass => $observerClass];
            })
            ->toArray();
    }

    /**
     * @param mixed $data The data, from the cache or dynamically loaded.
     */
    public function apply($data): void
    {
        /**
         * @var BaseModel|string $modelClass
         * @var string $observerClass
         */
        foreach ($data as $modelClass => $observerClass) {
            $modelClass::observe($observerClass);
        }
    }
}
