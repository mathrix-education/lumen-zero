<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Providers;

use Exception;
use HaydenPierce\ClassFinder\ClassFinder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Mathrix\Lumen\Zero\Models\BaseModel;
use Mathrix\Lumen\Zero\Utils\ClassResolver;
use function in_array;

/**
 * Automatically register policies.
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
        return Collection::make(ClassFinder::getClassesInNamespace(ClassResolver::$PoliciesNamespace))
            ->reject(static function (string $policyClass) {
                return in_array($policyClass, self::$IgnoredPolicies)
                    || ClassResolver::getModelClass($policyClass) === null;
            })
            ->mapWithKeys(static function ($policyClass) {
                /** @var BaseModel|null $modelClass */
                $modelClass = ClassResolver::getModelClass($policyClass);

                return [$modelClass => $policyClass];
            })
            ->toArray();
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
