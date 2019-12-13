<?php
declare(strict_types=1);

// Setup directories
$base = __DIR__ . '/../sandbox';
mkdirp($base);
$base = realpath($base);

$app = new Laravel\Lumen\Application($base);
$app->withFacades();
$app->withEloquent();

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    Laravel\Lumen\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    Laravel\Lumen\Exceptions\Handler::class
);

return $app;
