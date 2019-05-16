<?php

namespace Mathrix\Lumen\Zero\Providers;

use Exception;
use HaydenPierce\ClassFinder\ClassFinder;
use Illuminate\Support\ServiceProvider;
use Mathrix\Lumen\Zero\Models\BaseModel;
use Mathrix\Lumen\Zero\Utils\ClassResolver;

/**
 * Class ObserverServiceProvider.
 * Provides all observers to the corresponding models.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
class ObserverServiceProvider extends ServiceProvider
{
    /** @var array Ignored observers */
    public static $IgnoredObservers = [];


    /**
     * Auto-assign observers.
     *
     * @throws Exception
     */
    public function boot()
    {
        $observers = ClassFinder::getClassesInNamespace(ClassResolver::$ObserversNamespace);
        foreach ($observers as $observerClass) {
            /** @var BaseModel|null $modelClass */
            $modelClass = ClassResolver::getModelClass($observerClass);

            if ($modelClass !== null && !in_array($observerClass, self::$IgnoredObservers)) {
                $modelClass::observe($observerClass);
            }
        }
    }
}
