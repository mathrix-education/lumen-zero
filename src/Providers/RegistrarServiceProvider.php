<?php

namespace Mathrix\Lumen\Zero\Providers;

use Exception;
use HaydenPierce\ClassFinder\ClassFinder;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application;
use Mathrix\Lumen\Zero\Console\Commands\RoutesCacheCommand;
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
class RegistrarServiceProvider extends ServiceProvider
{
    public const ROUTES_CACHE_FILE = "bootstrap/routes.php";

    /** @var array Ignored registrars */
    public static $IgnoredRegistrars = [];


    /**
     * Auto-register routes from registrars.
     *
     * @throws Exception
     */
    public function register(): void
    {
        $this->commands([RoutesCacheCommand::class]);

        if (file_exists(app()->basePath(self::ROUTES_CACHE_FILE))) {
            $this->loadCachedRoutes();
        } else {
            $this->loadRegistrarRoutes();
        }
    }


    /**
     * Load cached routes file. Run `php artisan routes:cache` to build the route cache.
     */
    public function loadCachedRoutes()
    {
        $routesFile = app()->basePath(self::ROUTES_CACHE_FILE);

        $routes = require $routesFile;

        foreach ($routes as $route) {
            app()->router->addRoute(...array_values($route));
        }
    }


    /**
     * Load registrar routes (dynamically handled, but much, much slower).
     *
     * @throws Exception
     */
    public function loadRegistrarRoutes()
    {
        $registrars = ClassFinder::getClassesInNamespace(ClassResolver::$RegistrarNamespace);
        foreach ($registrars as $registrarClass) {
            if (!in_array($registrarClass, self::$IgnoredRegistrars)) {
                /** @var BaseRegistrar|string $registrar */
                $registrar = new $registrarClass($this->app->router);
                $registrar->register();
            }
        }
    }
}
