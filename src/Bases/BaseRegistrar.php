<?php

namespace Mathrix\Lumen\Bases;

use Illuminate\Support\Str;
use Laravel\Lumen\Routing\Router;

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
    /** @var Router */
    private $router;


    /**
     * BaseRegistrar constructor.
     * @param Router $router The Lumen Application Router.
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }


    /**
     * Pipe everything to the Router instance.
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
     * Register REST-style routes.
     *
     * @param string $base The base URL.
     * @param string $controller The controller
     * @param array $declarations The middleware
     */
    protected function rest(string $base, string $controller, array $declarations = []): void
    {
        $singular = Str::singular($base);

        foreach ($declarations as $key => $middleware) {
            switch ($key) {
                case "index":
                    $this->get("$base/{page:\d+}/{perPage:[1-9]\d*}", [
                        "uses" => "$controller@index",
                        "middleware" => $middleware ?? null
                    ]);
                    break;
                case "post":
                    $this->post("$base", [
                        "uses" => "$controller@post",
                        "middleware" => $middleware ?? null
                    ]);
                    break;
                case "get":
                case "patch":
                case "delete":
                    $this->{$key}("$base/{{$singular}Id:[1-9]\d*}", [
                        "uses" => "$controller@$key",
                        "middleware" => $middleware ?? null
                    ]);
                    break;
                default:
                    if (Str::start($key, "related:")) {
                        $relation = str_replace("related:", "", $key);
                        $this->get("$base/$relation/{page:\d+}/{perPage:[1-9]\d*}", [
                            "uses" => "$controller@$relation",
                            "middleware" => $middleware ?? null
                        ]);
                    }
            }
        }
    }
}
