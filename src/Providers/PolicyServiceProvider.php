<?php

namespace Mathrix\Lumen\Providers;

use Exception;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Mathrix\Lumen\Bases\BaseModel;
use Mathrix\Lumen\Exceptions\ClassNotFoundException;
use Mathrix\Lumen\Utils\ClassResolver;

/**
 * Class PolicyServiceProvider.
 * Automatically register policies.
 *
 * @author    Mathieu Bour <mathieu.tin.bour@gmail.com>
 * @since     1.0.1
 * @copyright Mathrix Education SA
 * @package   App\Providers
 */
class PolicyServiceProvider extends ServiceProvider
{
    /** @var array Ignored policies */
    public static $IgnoredPolicies = [];


    /**
     * @throws Exception
     */
    public function boot()
    {
        $policiesDir = $this->app->basePath() . "/app/Policies";
        $policyFiles = glob($policiesDir . DIRECTORY_SEPARATOR . "*.php");

        foreach ($policyFiles as $policyFile) {
            $policyClass = ClassResolver::$PoliciesNamespace . "\\" . mb_substr(basename($policyFile), 0, -4);

            if (in_array($policyClass, self::$IgnoredPolicies)) {
                continue;
            }

            /** @var string|BaseModel $modelClass */
            $modelClass = ClassResolver::getModelClassFrom("Policy", $policyClass);

            if (class_exists($modelClass)) {
                Gate::policy($modelClass, $policyClass);
            } else {
                throw new ClassNotFoundException($modelClass);
            }
        }
    }
}
