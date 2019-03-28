<?php

namespace Mathrix\Lumen\Providers;

use Illuminate\Support\ServiceProvider;
use Mathrix\Lumen\Bases\BaseModel;
use Mathrix\Lumen\Exceptions\ClassNotFoundException;
use Mathrix\Lumen\Utils\ClassResolver;

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
    /** @var array Ignored observers */
    public static $IgnoredObservers = [];


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
            $observerClass = ClassResolver::$ObserversNamespace . "\\" . mb_substr(basename($observerFile), 0, -4);

            /** @var string|BaseModel $modelClass */
            $modelClass = ClassResolver::getModelClassFrom("Observer", $observerClass);

            if (class_exists($modelClass) && !in_array($modelClass, self::$IgnoredObservers)) {
                $modelClass::observe($observerClass);
            } else {
                throw new ClassNotFoundException($modelClass);
            }
        }
    }
}
