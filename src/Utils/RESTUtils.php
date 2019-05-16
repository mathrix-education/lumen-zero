<?php

namespace Mathrix\Lumen\Zero\Utils;

use Illuminate\Support\Str;
use Mathrix\Lumen\Zero\Models\BaseModel;

/**
 * Class RESTUtils.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 2.0.0
 */
class RESTUtils
{
    /**
     * Resolve a REST route via a model and a key. Both are sufficient to deterministically determine the right method,
     * URI, field and relation.
     *
     * @param string $modelClass The Model class.
     * @param string $key The reset key.
     *
     * @return array in this order: method, uri, field and relation (which may be null)
     */
    public static function resolve(string $modelClass, string $key)
    {
        $singular = class_basename($modelClass);
        $plural = Str::plural($singular);
        $base = Str::lower($plural);

        /** @var BaseModel $model */
        $model = new $modelClass;

        $keyParts = explode(":", $key);
        [$type, $method] = $keyParts;
        $field = null;
        $relation = null;
        $uri = null;

        if ($type === "std") {
            $field = $keyParts[2] ?? $model->getKeyName();
            $relation = null;
            $identifier = lcfirst($singular) . ucfirst($field);

            switch ($method) {
                case "index":
                    $method = "get";
                    $uri = $base;
                    break;
                case "post":
                    $uri = $base;
                    break;
                case "get":
                case "patch":
                case "delete":
                    if ($field === $model->getKeyName()) {
                        $uri = "$base/{{$identifier}}";
                    } else {
                        $uri = "$base/$field/{{$identifier}}";
                    }
                    break;
            }
        } else if ($type === "rel") {
            if (count($keyParts) === 3) {
                // Key shape: rel:{method}:{relation}
                $field = $model->getKeyName();
                $relation = $keyParts[2];
            } else if (count($keyParts) === 4) {
                // Key shape: rel:{method}:{field}:{relation}
                $field = $keyParts[2];
                $relation = $keyParts[3];
            }

            $identifier = lcfirst($singular) . ucfirst($field);

            // GET and PATCH only
            if ($field === $model->getKeyName()) {
                $uri = "$base/{{$identifier}}/$relation";
            } else {
                $uri = "$base/$field/{{$identifier}}/$relation";
            }
        }

        return [$method, $uri, $field, $relation];
    }
}
