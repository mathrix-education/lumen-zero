<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Providers;

use Laravel\Lumen\Application;
use Laravel\Lumen\Routing\Router;
use Mathrix\Lumen\Zero\Registrars\BaseRegistrar;
use Mathrix\Lumen\Zero\Utils\ClassResolver;
use function app;
use function array_values;
use function in_array;

/**
 * Register and cache the routes declared in the registrars.
 * By default, the provider will look for classes in the App\Registrars.
 *
 * @property Application $app
 */
class RegistrarServiceProvider extends CacheableServiceProvider
{
    public const CACHE_FILE = 'bootstrap/cache/routes.php';

    /** @var array Ignored registrars */
    public static $IgnoredRegistrars = [];

    /**
     * @return array Dynamically load the routes from the registrars.
     */
    public function loadDynamic(): array
    {
        $router     = new Router(app());
        $registrars = ClassResolver::getClassesInNamespace(config('zero.namespaces.registrars'));

        foreach ($registrars as $registrar) {
            if (in_array($registrar, self::$IgnoredRegistrars)) {
                continue;
            }

            /** @var BaseRegistrar $instance */
            $instance = new $registrar($router);
            $instance->register();
        }

        return array_map(static function (array $route) {
            return array_values($route);
        }, array_values($router->getRoutes()));
    }

    /**
     * @param array $routes The data, from the cache or dynamically loaded.
     */
    public function apply($routes): void
    {
        foreach ($routes as $route) {
            app()->router->addRoute(...array_values($route));
        }
    }
}
