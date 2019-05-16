<?php

namespace Mathrix\Lumen\Zero\Utils;

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
    /** @var string $ControllersNamespace The controllers namespace. */
    public static $ControllersNamespace = "App\\Controllers";
    /** @var string $ModelsNamespace The models namespace. */
    public static $ModelsNamespace = "App\\Models";
    /** @var string $ObserversNamespace The observers namespace. */
    public static $ObserversNamespace = "App\\Observers";
    /** @var string $PoliciesNamespace The policies namespace. */
    public static $PoliciesNamespace = "App\\Policies";
    /** @var string $RegistrarNamespace The registrars namespace. */
    public static $RegistrarNamespace = "App\\Registrars";
    /** @var array $KnownCallers The possible callers. */
    public static $KnownCallers = ["Controller", "Policy", "Test", "Registrar"];


    /**
     * Get the model associated with a given class.
     *
     * @param string|object $callerClass The caller class
     * @param bool $force If set to true, return the model class i=even if does not exist.
     *
     * @return string|null The full model class if found, null otherwise.
     */
    public static function getModelClass($callerClass, $force = false): ?string
    {
        $classBaseName = class_basename($callerClass);
        $potentialModel = Str::singular(str_replace(self::$KnownCallers, "", $classBaseName));
        $potentialModelClass = self::$ModelsNamespace . "\\" . $potentialModel;
        $exists = class_exists($potentialModelClass);

        if ($force || $exists) {
            return $potentialModelClass;
        } else {
            return null;
        }
    }
}
