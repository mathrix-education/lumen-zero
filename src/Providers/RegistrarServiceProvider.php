<?php

namespace Mathrix\Lumen\Providers;

use Illuminate\Support\ServiceProvider;
use Mathrix\Lumen\Bases\BaseRegistrar;
use Mathrix\Lumen\Exceptions\ClassNotFoundException;

/**
 * Class RegistrarServiceProvider.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 *
 * @property \Laravel\Lumen\Application $app
 */
class RegistrarServiceProvider extends ServiceProvider
{
    private const REGISTRARS_DIRECTORY = "app/Registrars";
    private const REGISTRARS_NAMESPACE = "App\\Registrars\\";
    private const EXCLUDED_POLICIES = [];


    /**
     * Register all Registrars.
     * @throws \Exception
     */
    public function register(): void
    {
        $registrarsPath = $this->app->basePath() . "/" . self::REGISTRARS_DIRECTORY;
        $registrarFiles = glob($registrarsPath . \DIRECTORY_SEPARATOR . "*.php");

        foreach ($registrarFiles as $registrarFile) {
            $registrarClass = self::REGISTRARS_NAMESPACE . mb_substr(basename($registrarFile), 0, -4);

            if (\in_array($registrarClass, self::EXCLUDED_POLICIES, true)) {
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
