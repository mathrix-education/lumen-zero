<?php

namespace Mathrix\Lumen\Zero\Providers;

use Exception;
use HaydenPierce\ClassFinder\ClassFinder;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application;
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
    /** @var array Ignored registrars */
    public static $IgnoredRegistrars = [];


    /**
     * Auto-register routes from registrars.
     *
     * @throws Exception
     */
    public function register(): void
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
