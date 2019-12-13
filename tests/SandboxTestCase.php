<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Tests;

use Laravel\Lumen\Application;
use Laravel\Lumen\Testing\TestCase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

abstract class SandboxTestCase extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * Creates the application.
     * Needs to be implemented by subclasses.
     *
     * @return Application
     */
    public function createApplication(): Application
    {
        return require __DIR__ . '/bootstrap.php';
    }
}
