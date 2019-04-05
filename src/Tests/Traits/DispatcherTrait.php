<?php

namespace Mathrix\Lumen\Tests\Traits;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

/**
 * Trait DispatcherTrait.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
trait DispatcherTrait
{
    /** @var Dispatcher $dispatcher */
    private $dispatcher;


    /**
     * Dispatch an uri.
     *
     * @param string $method The method.
     * @param string $uri The uri.
     *
     * @return array
     */
    protected function dispatch(string $method, string $uri): array
    {
        $method = mb_strtoupper($method);

        return $this->getDispatcher()->dispatch($method, $uri);
    }




    /**
     * Get the Dispatcher
     * @return Dispatcher
     */
    protected function getDispatcher(): Dispatcher
    {
        if (!$this->dispatcher instanceof Dispatcher) {
            $this->dispatcher = simpleDispatcher(function (RouteCollector $r) {
                foreach (app()->router->getRoutes() as $route) {
                    $r->addRoute($route["method"], $route["uri"], $route["action"]);
                }
            });
        }

        return $this->dispatcher;
    }
}
