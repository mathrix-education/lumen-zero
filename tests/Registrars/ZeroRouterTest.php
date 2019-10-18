<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Tests\Registrars;

use Mathrix\Lumen\Zero\Exceptions\InvalidArgument;
use Mathrix\Lumen\Zero\Registrars\ZeroRouter;
use Mathrix\Lumen\Zero\Testing\ModelMockFactory;
use PHPUnit\Framework\TestCase;
use function class_exists;

/**
 * @coversDefaultClass \Mathrix\Lumen\Zero\Registrars\ZeroRouter
 */
class ZeroRouterTest extends TestCase
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
        $modelClass = 'App\\Models\\Pear';

        if (!class_exists($modelClass)) {
            ModelMockFactory::make()
                ->setName('Pear')
                ->setMethod('public', 'getKeyName', 'id')
                ->compile()
                ->exec();
        }

        [$actualMethod, $actualUri] = ZeroRouter::resolve($key, $modelClass);

        $this->assertEquals($expectedMethod, $actualMethod);
        $this->assertEquals($expectedUri, $actualUri);
    }
}
