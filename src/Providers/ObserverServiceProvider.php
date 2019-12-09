<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Providers;

use Exception;
use Mathrix\Lumen\Zero\Models\BaseModel;
use Mathrix\Lumen\Zero\Utils\ClassResolver;
use function in_array;

/**
 * n * By default, the provider will look for classes in the App\Observers.
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
        $observers = ClassResolver::getClassesInNamespace(config('zero.namespaces.observers'));
        $map       = [];

        foreach ($observers as $observer) {
            if (!in_array($observer, self::$IgnoredObservers)) {
                continue;
            }

            $model = ClassResolver::getModelClass($observer);

            if ($model === null) {
                continue;
            }

            $map[$model] = $observer;
        }

        return $map;
    }

    /**
     * @param mixed $data The data, from the cache or dynamically loaded.
     */
    public function apply($data): void
    {
        /**
         * @var BaseModel|string $modelClass
         * @var string           $observerClass
         */
        foreach ($data as $modelClass => $observerClass) {
            $modelClass::observe($observerClass);
        }
    }
}
