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
    protected const PATTERN_ID = "[1-9]\d*";

    /** @var array The default permissions (Passport scope) */
    public static $DefaultPermissions = [
        "index" => null,
        "get" => null,
        "patch" => null,
        "post" => null,
        "delete" => null
    ];
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
     * @param $base
     * @param $controller
     * @param $permissions
     */
    protected function rest($base, $controller, $permissions)
    {
        $permissions = array_merge(self::$DefaultPermissions, $permissions);
        $singular = Str::singular($base);

        $this->get("{$base}[/{page:[1-9]\d*}/{perPage:[1-9]\d*]", [
            "middleware" => !empty($permissions["index"]) ? "scope:{$permissions["index"]}" : null,
            "uses" => "$controller@index"
        ]);

        $this->post("$base", [
            "middleware" => !empty($permissions["post"]) ? "scope:{$permissions["post"]}" : null,
            "uses" => "$controller@post"
        ]);

        foreach (["get", "patch", "delete"] as $method) {
            $this->{$method}("$base/{{$singular}Id}:[1-9]\d*", [
                "middleware" => !empty($permissions[$method]) ? "scope:{$permissions[$method]}" : null,
                "uses" => "$controller@$method"
            ]);
        }
    }


    /**
     * Register the routes.
     */
    abstract public function register(): void;
}
