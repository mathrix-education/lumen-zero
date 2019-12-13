<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Providers;

use Laravel\Lumen\Application;
use Laravel\Lumen\Routing\Router;
use Mathrix\Lumen\Zero\Registrars\BaseRegistrar;
use Mathrix\Lumen\Zero\Utils\ClassResolver;
use function array_diff;
use function array_map;
use function array_values;
use function config;

/**
 * Register and cache the routes declared in the registrars.
 *
 * @property Application $app
 */
class RegistrarServiceProvider extends CacheableServiceProvider
{
    public const CACHE_FILE = 'bootstrap/cache/routes.php';

    /**
     * @return array Dynamically load the routes from the registrars.
     */
    public function loadDynamic(): array
    {
        $router     = new Router($this->app);
        $namespace  = config('zero.namespaces.registrars', '\\App\\Registrars');
        $registrars = array_diff(
            ClassResolver::getClassesInNamespace($namespace),
            config('zero.ignore.registrars', [])
        );

        foreach ($registrars as $registrar) {
            /** @var BaseRegistrar $instance */
            $instance = new $registrar($router);
            $instance->register();
        }

        return array_map(fn(array $route) => array_values($route), array_values($router->getRoutes()));
    }

    /**
     * @param array $routes The data, from the cache or dynamically loaded.
     */
    public function apply($routes): void
    {
        foreach ($routes as $route) {
            $this->app->router->addRoute(...array_values($route));
        }
    }
}
