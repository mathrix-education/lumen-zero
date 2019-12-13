<?php


namespace Mathrix\Lumen\Zero\Tests\Providers;

use Brick\VarExporter\ExportException;
use Laravel\Lumen\Application;
use Mathrix\Lumen\Zero\Providers\CacheableServiceProvider;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;

/**
 * @coversDefaultClass \Mathrix\Lumen\Zero\Providers\CacheableServiceProvider
 */
class CacheableServiceProviderTest extends MockeryTestCase
{
    /**
     * @covers ::register
     * @throws ExportException
     */
    public function testRegister(): void
    {
        $app = Mockery::mock(Application::class);

        /** @var CacheableServiceProvider|MockInterface $instance */
        $instance = Mockery::mock(CacheableServiceProvider::class, [$app]);
        $instance->shouldReceive('isCached')->twice()->andReturn(true);
        $instance->shouldReceive('loadCache')->once()->andReturnNull();
        $instance->shouldReceive('apply')->once()->andReturnNull();

        $instance->boot();
    }
}
