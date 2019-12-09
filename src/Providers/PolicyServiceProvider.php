<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Providers;

use Exception;
use Illuminate\Support\Facades\Gate;
use Mathrix\Lumen\Zero\Utils\ClassResolver;
use function in_array;

/**
 * Automatically register policies.
 * By default, the provider will look for classes in the App\Policies.
 */
class PolicyServiceProvider extends CacheableServiceProvider
{
    public const CACHE_FILE = 'bootstrap/cache/policies.php';

    /** @var array Ignored policies */
    public static $IgnoredPolicies = [];

    /**
     * @return array Dynamically load polices.
     *
     * @throws Exception
     */
    public function loadDynamic()
    {
        $policies = ClassResolver::getClassesInNamespace(config('zero.namespaces.policies'));
        $map      = [];

        foreach ($policies as $policy) {
            if (!in_array($policy, self::$IgnoredPolicies)) {
                continue;
            }

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
