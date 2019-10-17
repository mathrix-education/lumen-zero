<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Registrars;

use Illuminate\Support\Str;
use Laravel\Lumen\Routing\Router;
use Mathrix\Lumen\Zero\Exceptions\InvalidArgument;
use Mathrix\Lumen\Zero\Utils\ClassResolver;
use function call_user_func_array;
use function class_basename;

/**
 * Allow object oriented routes declaration.
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
     * @param Router $router The Lumen Application Router.
     */
    public function __construct(Router &$router)
    {
        $this->router     = $router;
        $this->modelClass = ClassResolver::getModelClass($this);
    }

    /**
     * Forward everything to the Router instance.
     *
     * @param string $name      The method name
     * @param array  $arguments The method arguments
     */
    public function __call(string $name, array $arguments)
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
     * list
     * create
     * read
     * update
     * delete
     * read:{relation}
     * reorder:{relation}
     *
     * @param string $key        The key.
     * @param null   $middleware The route middleware.
     *
     * @throws InvalidArgument
     */
    public function registerCRUDRoute(string $key, $middleware = null)
    {
        [$method, $uri] = ZeroRouter::resolve($key, $this->modelClass);

        $plural     = Str::plural(class_basename($this->modelClass));
        $controller = ClassResolver::$ControllersNamespace . "\\{$plural}Controller";

        $this->{$method}($uri, [
            'uses'       => $controller, // We will use $controller::_invoke();
            'middleware' => $middleware ?? null,
        ]);
    }

    /**
     * Register CRUD-style routes.
     *
     * @param array $declarations The middleware
     *
     * @throws InvalidArgument
     */
    protected function registerCRUDRoutes(array $declarations = []): void
    {
        foreach ($declarations as $key => $middleware) {
            $this->registerCRUDRoute($key, $middleware);
        }
    }
}
