<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Tests\Registrars;

use Mathrix\Lumen\Zero\Exceptions\InvalidArgument;
use Mathrix\Lumen\Zero\Registrars\ZeroRouter;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use function get_class;

/**
 * @coversDefaultClass \Mathrix\Lumen\Zero\Registrars\ZeroRouter
 */
class ZeroRouterTest extends MockeryTestCase
{
    public function resolveDataProvider()
    {
        return [
            'list'           => ['list', 'get', '/pears'],
            'create'         => ['create', 'post', '/pears'],
            'read'           => ['read', 'get', '/pears/{pearId}'],
            'update'         => ['update', 'patch', '/pears/{pearId}'],
            'delete'         => ['delete', 'delete', '/pears/{pearId}'],
            'read:brands'    => ['read:brands', 'get', '/pears/{pearId}/brands'],
            'reorder:brands' => ['reorder:brands', 'patch', '/pears/{pearId}/brands'],
        ];
    }

    /**
     * @param string $key
     * @param string $expectedMethod
     * @param string $expectedUri
     *
     * @throws InvalidArgument
     *
     * @dataProvider resolveDataProvider
     * @covers ::resolve
     */
    public function testResolve(string $key, string $expectedMethod, string $expectedUri)
    {
        $model = Mockery::mock('overload:\Pear');
        $model->shouldReceive('getKeyName')->withNoArgs()->andReturn('id')->once();
        [$actualMethod, $actualUri] = ZeroRouter::resolve($key, get_class($model));

        $this->assertEquals($expectedMethod, $actualMethod);
        $this->assertEquals($expectedUri, $actualUri);
    }
}
