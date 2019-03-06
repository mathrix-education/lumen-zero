<?php
namespace Mathrix\Lumen\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Mathrix\Lumen\Bases\BaseModel;
use Mathrix\Lumen\Exceptions\ClassNotFoundException;

/**
 * Class PolicyServiceProvider.
 * Automatically register policies.
 *
 * @author    Mathieu Bour <mathieu.tin.bour@gmail.com>
 * @author    Jérémie Levain <munezero999@live.fr>
 * @since     1.0.1
 * @copyright Mathrix Education SA
 * @package   App\Providers
 */
class PolicyServiceProvider extends ServiceProvider
{
    private const MODELS_NAMESPACE = "App\\Models\\";
    private const POLICIES_NAMESPACE = "App\\Policies\\";
    private const EXCLUDED_POLICIES = [];

    /**
     * @throws \Exception
     */
    public function boot()
    {
        $policiesDir = $this->app->basePath() . "/app/Policies";
        $policyFiles = glob($policiesDir . \DIRECTORY_SEPARATOR . "*.php");

        foreach ($policyFiles as $policyFile) {
            $policyClass = self::POLICIES_NAMESPACE . mb_substr(basename($policyFile), 0, -4);

            if (\in_array($policyClass, self::EXCLUDED_POLICIES, true)) {
                continue;
            }

            /** @var string|BaseModel $modelClass */
            $modelPlural = str_replace([self::POLICIES_NAMESPACE, "Policy"], "", $policyClass);
            $modelSingular = Str::singular($modelPlural);
            $modelClass = self::MODELS_NAMESPACE . $modelSingular;

            if (class_exists($modelClass)) {
                Gate::policy($modelClass, $policyClass);
            } else {
                throw new ClassNotFoundException($modelClass);
            }
        }
    }
}
