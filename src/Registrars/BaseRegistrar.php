<?php

namespace Mathrix\Lumen\Zero\Registrars;

use Illuminate\Support\Str;
use Laravel\Lumen\Routing\Router;
use Mathrix\Lumen\Zero\Utils\ClassResolver;
use Mathrix\Lumen\Zero\Utils\RESTUtils;

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
    public function __construct(Router &$router)
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
        [$method, $uri] = RESTUtils::resolve($this->modelClass, $key);
        $plural = Str::plural(class_basename($this->modelClass));

        $controller = ClassResolver::$ControllersNamespace . "\\{$plural}Controller";

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
