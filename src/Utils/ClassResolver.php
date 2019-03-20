<?php

namespace Mathrix\Lumen\Utils;

use Illuminate\Support\Str;

/**
 * Class ClassResolver.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
class ClassResolver
{
    /** @var string $ModelsNamespace The models namespace. */
    public static $ModelsNamespace = "App\\Models";
    /** @var string $ObserversNamespace The observers namespace. */
    public static $ObserversNamespace = "App\\Observers";
    /** @var string $PoliciesNamespace The policies namespace. */
    public static $PoliciesNamespace = "App\\Policies";
    /** @var string $RegistrarNamespace The registrars namespace. */
    public static $RegistrarNamespace = "App\\Registrars";


    /**
     * Get model full class name
     *
     * @param string $modelName
     * @return string
     */
    public static function getModelClass(string $modelName): string
    {
        return self::$ModelsNamespace . "\\" . Str::ucfirst($modelName);
    }


    /**
     * Get the Model class from a full class.
     *
     * @param string $type
     * @param string $fullClass
     * @return string
     */
    public static function getModelClassFrom(string $type, string $fullClass)
    {
        $parts = explode("\\", $fullClass);
        $className = array_pop($parts);
        $modelName = Str::singular(str_replace($type, "", $className));
        return self::$ModelsNamespace . "\\$modelName";
    }
}
