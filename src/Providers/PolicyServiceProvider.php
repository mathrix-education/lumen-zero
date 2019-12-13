<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Providers;

use Exception;
use Illuminate\Support\Facades\Gate;
use Mathrix\Lumen\Zero\Utils\ClassResolver;
use function array_diff;
use function config;

/**
 * Automatically register policies.
 */
class PolicyServiceProvider extends CacheableServiceProvider
{
    public const CACHE_FILE = 'bootstrap/cache/policies.php';

    /**
     * @return array Dynamically load polices.
     *
     * @throws Exception
     */
    public function loadDynamic()
    {
        $policies = array_diff(
            ClassResolver::getClassesInNamespace(config('zero.namespaces.policies')),
            config('zero.ignore.policies', [])
        );
        $map      = [];

        foreach ($policies as $policy) {
            $model = ClassResolver::getModelClass($policy);

            if ($model === null) {
                continue;
            }

            $map[$model] = $policy;
        }

        return $map;
    }

    /**
     * @param mixed $data The data, from the cache or dynamically loaded.
     *
     * @return mixed
     */
    public function apply($data): void
    {
        foreach ($data as $modelClass => $policyClass) {
            Gate::policy($modelClass, $policyClass);
        }
    }
}
