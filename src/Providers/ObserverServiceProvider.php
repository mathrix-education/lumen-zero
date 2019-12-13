<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Providers;

use Exception;
use Mathrix\Lumen\Zero\Models\BaseModel;
use Mathrix\Lumen\Zero\Utils\ClassResolver;
use function array_diff;
use function config;

/**
 * Link the observers with their associated model.
 */
class ObserverServiceProvider extends CacheableServiceProvider
{
    public const CACHE_FILE = 'bootstrap/cache/observers.php';

    /**
     * @return array Dynamically load the model observers.
     *
     * @throws Exception
     */
    public function loadDynamic(): array
    {
        $observers = array_diff(
            ClassResolver::getClassesInNamespace(config('zero.namespaces.observers')),
            config('zero.ignored.observers', [])
        );
        $map       = [];

        foreach ($observers as $observer) {
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
