<?php

namespace Mathrix\Lumen\Zero\Providers;

use Exception;
use HaydenPierce\ClassFinder\ClassFinder;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Mathrix\Lumen\Zero\Models\BaseModel;
use Mathrix\Lumen\Zero\Utils\ClassResolver;

/**
 * Class PolicyServiceProvider.
 * Automatically register policies.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.1
 */
class PolicyServiceProvider extends ServiceProvider
{
    /** @var array Ignored policies */
    public static $IgnoredPolicies = [];


    /**
     * Auto-assign policies.
     *
     * @throws Exception
     */
    public function boot()
    {
        $policies = ClassFinder::getClassesInNamespace(ClassResolver::$PoliciesNamespace);
        foreach ($policies as $policyClass) {
            /** @var BaseModel|null $modelClass */
            $modelClass = ClassResolver::getModelClass($policyClass);

            if ($modelClass !== null && !in_array($policyClass, self::$IgnoredPolicies)) {
                Gate::policy($modelClass, $policyClass);
            }
        }
    }
}
