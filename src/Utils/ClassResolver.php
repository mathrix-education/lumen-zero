<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Utils;

use Exception;
use HaydenPierce\ClassFinder\ClassFinder;
use Illuminate\Support\Str;
use Mathrix\Lumen\Zero\Models\BaseModel;
use function array_shift;
use function class_basename;
use function class_exists;
use function config;
use function count;
use function explode;

class ClassResolver
{
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
            $potentialModelClass = config('zero.namespaces.models') . "\\$potentialModel";

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

    /**
     * Get the classes in a given name space. Return an empty array if the namespace could not be found.
     *
     * @param string $namespace The namespace to explore.
     *
     * @return string[] The classes full qualified names.
     */
    public static function getClassesInNamespace(string $namespace): array
    {
        try {
            return ClassFinder::getClassesInNamespace($namespace);
        } catch (Exception $exception) {
            return [];
        }
    }
}
