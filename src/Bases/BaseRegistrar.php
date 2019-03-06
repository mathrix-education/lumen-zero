<?php

namespace Mathrix\Lumen\Bases;

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
}
