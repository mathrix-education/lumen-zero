<?php

namespace Mathrix\Lumen\Utils;

use Illuminate\Support\Str;

/**
 * Class Resolver.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
class Resolver
{
    private static $ModelsNamespace = "App\\Models\\";


    /**
     * Set the models namespace globally.
     * @param string $newModelNamespace The new models namespace.
     */
    public function setModelNamespace(string $newModelNamespace): void
    {
        if (!ends_with($newModelNamespace, "\\")) {
            $newModelNamespace .= "\\";
        }

        self::$ModelsNamespace = $newModelNamespace;
    }


    /**
     * Get model full class name
     * @param string $modelName
     * @return string
     */
    public static function getModelClass(string $modelName): string
    {
        return self::$ModelsNamespace . $modelName;
    }


    public static function modelClassFrom(string $type, string $fullClass)
    {
        $parts = explode("\\", $fullClass);
        $className = array_pop($parts);
        $modelName = Str::singular(str_replace($type, "", $className));
        return self::$ModelsNamespace . "\\$modelName";
    }


    public static function modelFromController(string $controllerClass)
    {
        return self::modelClassFrom("Controller", $controllerClass);
    }


    public static function modelFromTest(string $testClass)
    {
        return self::modelClassFrom("Test", $testClass);
    }
}
