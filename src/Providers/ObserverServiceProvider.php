<?php

namespace Mathrix\Lumen\Providers;

use Illuminate\Support\ServiceProvider;
use Mathrix\Lumen\Bases\BaseModel;
use Mathrix\Lumen\Exceptions\ClassNotFoundException;

/**
 * Class ObserverServiceProvider.
 * Provides all observers to the corresponding models.
 *
 * @author    Mathieu Bour <mathieu.tin.bour@gmail.com>
 * @since     1.0.0
 * @copyright Mathrix Education SA
 * @package   App\Providers
 */
class ObserverServiceProvider extends ServiceProvider
{
    private const MODELS_NAMESPACE = "App\\Models\\";
    private const OBSERVERS_NAMESPACE = "App\\Observers\\";
    private const EXCLUDED_OBSERVERS = [];

    /**
     * Auto-discover observers based on the filename.
     *
     * @throws \Exception
     */
    public function boot()
    {
        $observersDir = $this->app->basePath() . "/app/Observers";
        $observerFiles = glob($observersDir . \DIRECTORY_SEPARATOR . "*.php");

        foreach ($observerFiles as $observerFile) {
            $observerClass = self::OBSERVERS_NAMESPACE . mb_substr(basename($observerFile), 0, -4);

            if (\in_array($observerClass, self::EXCLUDED_OBSERVERS, true)) {
                continue;
            }

            /** @var string|BaseModel $modelClass */
            $modelClass = str_replace([self::OBSERVERS_NAMESPACE, "Observer"], [self::MODELS_NAMESPACE, ""],
                $observerClass);

            if (class_exists($modelClass)) {
                $modelClass::observe($observerClass);
            } else {
                throw new ClassNotFoundException($modelClass);
            }
        }
    }
}
