<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Utils;

use Illuminate\Support\Str;
use Mathrix\Lumen\Zero\Models\BaseModel;
use function array_shift;
use function class_basename;
use function class_exists;
use function count;
use function explode;

class ClassResolver
{
    /** @var string $ControllersNamespace The controllers namespace. */
    public static $ControllersNamespace = 'App\\Controllers';
    /** @var string $ModelsNamespace The models namespace. */
    public static $ModelsNamespace = 'App\\Models';
    /** @var string $ObserversNamespace The observers namespace. */
    public static $ObserversNamespace = 'App\\Observers';
    /** @var string $PoliciesNamespace The policies namespace. */
    public static $PoliciesNamespace = 'App\\Policies';
    /** @var string $RegistrarNamespace The registrars namespace. */
    public static $RegistrarNamespace = 'App\\Registrars';

    /**
     * Get the model associated with a given class.
     *
     * @param string|object $callerClass The caller class
     * @param bool          $force       If set to true, return the model class i=even if does not exist.
     *
     * @return BaseModel|string|null The full model class if found, null otherwise.
     */
    public static function getModelClass($callerClass, $force = false): ?string
    {
        $snakedClass    = Str::snake(class_basename($callerClass));
        $parts          = explode('_', $snakedClass);
        $partsCount     = count($parts);
        $potentialModel = '';

        /*
         * Model have multiple words, line LineItem.
         * If we pass LineItemObserver as $callerClass, we would get ["Line", "Item", "Observer"].
         */
        while (count($parts) > 1 || $partsCount === 1) {
            $part                = array_shift($parts);
            $potentialModel     .= Str::singular(Str::ucfirst($part));
            $potentialModelClass = self::$ModelsNamespace . "\\$potentialModel";

            $exists = class_exists($potentialModelClass);

            if ($force || $exists) {
                return $potentialModelClass;
            }

            if ($partsCount === 1) {
                return null;
            }
        }

        return null;
    }
}
