<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Registrars;

use Illuminate\Support\Str;
use Mathrix\Lumen\Zero\Exceptions\InvalidArgument;
use Mathrix\Lumen\Zero\Models\BaseModel;
use function class_basename;
use function count;
use function explode;
use function lcfirst;
use function ucfirst;

class ZeroRouter
{
    /**
     * @param string $key
     * @param string $modelClass
     *
     * @return array
     *
     * @throws InvalidArgument
     */
    public static function resolve(string $key, string $modelClass): array
    {
        $singular = class_basename($modelClass);
        $plural   = Str::plural($singular);
        $base     = Str::lower($plural);
        $parts    = explode(':', $key);
        /** @var BaseModel $model */
        $model      = new $modelClass();
        $identifier = lcfirst($singular) . ucfirst($model->getKeyName());

        $method = null;
        $uri    = null;

        if (count($parts) === 1) {
            // L-CRUD
            switch ($parts[0]) {
                case 'list':
                    $method = 'get';
                    $uri    = "/$base";
                    break;
                case 'create':
                    $method = 'post';
                    $uri    = "/$base";
                    break;
                case 'read':
                    $method = 'get';
                    $uri    = "/$base/{{$identifier}}";
                    break;
                case 'update':
                    $method = 'patch';
                    $uri    = "/$base/{{$identifier}}";
                    break;
                case 'delete':
                    $method = 'delete';
                    $uri    = "/$base/{{$identifier}}";
                    break;
                default:
                    throw new InvalidArgument(
                        [],
                        "key part 1 has to be in [list, create, read, update, delete], got: {$parts[0]}"
                    );
            }
        } elseif (count($parts) === 2) {
            // Relations
            $relation = $parts[1];
            switch ($parts[0]) {
                case 'read':
                    $method = 'get';
                    $uri    = "/$base/{{$identifier}}/$relation";
                    break;
                case 'reorder':
                    $method = 'patch';
                    $uri    = "/$base/{{$identifier}}/$relation";
                    break;
                default:
                    throw new InvalidArgument(
                        [],
                        "key part 1 has to be in [read, reorder], got: {$parts[0]}"
                    );
            }
        } else {
            throw new InvalidArgument([], 'key cannot have more than two parts');
        }

        return [$method, $uri];
    }
}
