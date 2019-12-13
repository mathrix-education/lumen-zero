<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Tests\Console\Commands;

use Illuminate\Support\Facades\Artisan;
use Mathrix\Lumen\Zero\Providers\ZeroServiceProvider;
use Mathrix\Lumen\Zero\Tests\SandboxTestCase;

/**
 * @coversDefaultClass \Mathrix\Lumen\Zero\Console\Commands\ProvidersCacheCommand
 */
class ProvidersCacheCommandTest extends SandboxTestCase
{
    public function testHandle(): void
    {
        $this->app->register(ZeroServiceProvider::class);

        Artisan::call('providers:cache -f');

        $this->assertFileExists($this->app->basePath('bootstrap/cache/observers.php'));
        $this->assertFileExists($this->app->basePath('bootstrap/cache/policies.php'));
        $this->assertFileExists($this->app->basePath('bootstrap/cache/routes.php'));
    }
}
