<?php

namespace Mathrix\Lumen\Zero\Providers;

use Exception;
use HaydenPierce\ClassFinder\ClassFinder;
use Illuminate\Support\Collection;
use Laravel\Lumen\Application;
use Laravel\Lumen\Routing\Router;
use Mathrix\Lumen\Zero\Console\Commands\providers;
use Mathrix\Lumen\Zero\Registrars\BaseRegistrar;
use Mathrix\Lumen\Zero\Utils\ClassResolver;

/**
 * Class RegistrarServiceProvider.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 *
 * @property Application $app
 */
class RegistrarServiceProvider extends CacheableServiceProvider
{
    public const CACHE_FILE = "bootstrap/cache/routes.php";

    /** @var array Ignored registrars */
    public static $IgnoredRegistrars = [];


    /**
     * @return array Dynamically load the routes from the registrars.
     * @throws Exception
     */
    public function loadDynamic(): array
    {
        $router = new Router(app());

        // Load routes from registrar
        Collection::make(ClassFinder::getClassesInNamespace(ClassResolver::$RegistrarNamespace))
            ->reject(function (string $registrarClass) {
                return in_array($registrarClass, self::$IgnoredRegistrars);
            })
            ->each(function (string $registrarClass) use (&$router) {
                /** @var BaseRegistrar|string $registrar */
                $registrar = new $registrarClass($router);
                $registrar->register();
            });

        return Collection::make($router->getRoutes())
            ->values()
            ->map(function (array $route) {
                return array_values($route);
            })
            ->toArray();
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
