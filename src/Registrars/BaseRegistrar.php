<?php

namespace Mathrix\Lumen\Zero\Registrars;

use Illuminate\Support\Str;
use Laravel\Lumen\Routing\Router;
use Mathrix\Lumen\Zero\Models\BaseModel;
use Mathrix\Lumen\Zero\Utils\ClassResolver;

/**
 * Class BaseRegistrar.
 * Allow object oriented routes declaration.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 *
 * @mixin Router
 */
abstract class BaseRegistrar
{
    /** @var string|null The associated model class. */
    protected $modelClass;
    /** @var Router */
    private $router;


    /**
     * BaseRegistrar constructor.
     *
     * @param Router $router The Lumen Application Router.
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
        $this->modelClass = ClassResolver::getModelClass($this);
    }


    /**
     * Pipe everything to the Router instance.
     *
     * @param $name
     * @param $arguments
     */
    public function __call($name, $arguments)
    {
        call_user_func_array([$this->router, $name], $arguments);
    }


    /**
     * Register the routes.
     */
    abstract public function register(): void;


    /**
     * Register a route based on the route key.
     * Example of keys:
     * std:{method}
     * std:{method}:{field}
     * rel:{method}:{relation}
     * rel:{method}:{field}:{relation}
     *
     * @param string $key The key.
     * @param null $middleware The route middleware.
     */
    public function makeRESTRoute(string $key, $middleware = null)
    {
        $singular = class_basename($this->modelClass);
        $plural = Str::plural($singular);
        $base = Str::lower($plural);
        $controller = ClassResolver::$ControllersNamespace . "\\{$plural}Controller";

        /** @var BaseModel $model */
        $model = new $this->modelClass();

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

        $this->{$method}($uri, [
            "uses" => $controller, // We will use $controller::_invoke();
            "middleware" => $middleware ?? null
        ]);
    }


    /**
     * Register REST-style routes.
     *
     * @param array $declarations The middleware
     */
    protected function rest(array $declarations = []): void
    {
        foreach ($declarations as $key => $middleware) {
            $this->makeRESTRoute($key, $middleware);
        }
    }
}
