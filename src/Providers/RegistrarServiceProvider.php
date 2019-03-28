<?php

namespace Mathrix\Lumen\Providers;

use Exception;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application;
use Mathrix\Lumen\Bases\BaseRegistrar;
use Mathrix\Lumen\Exceptions\ClassNotFoundException;
use Mathrix\Lumen\Utils\ClassResolver;

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
     * Register all Registrars.
     * @throws Exception
     */
    public function register(): void
    {
        $registrarsPath = $this->app->basePath() . "/app/Registrars";
        $registrarFiles = glob($registrarsPath . DIRECTORY_SEPARATOR . "*.php");

        foreach ($registrarFiles as $registrarFile) {
            $registrarClass = ClassResolver::$RegistrarNamespace . "\\" . mb_substr(basename($registrarFile), 0, -4);

            if (in_array($registrarClass, self::$IgnoredRegistrars)) {
                continue;
            }

            if (class_exists($registrarClass)) {
                /** @var string|BaseRegistrar $registrar */
                $registrar = new $registrarClass($this->app->router);
                $registrar->register();
            } else {
                throw new ClassNotFoundException($registrarClass);
            }
        }
    }
}
