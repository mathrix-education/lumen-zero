<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Tests\Controllers;

use Illuminate\Http\Request;
use Mathrix\Lumen\Zero\Controllers\BaseController;
use Mathrix\Lumen\Zero\Testing\Traits\Reflector;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BaseControllerTest extends TestCase
{
    use Reflector;

    public function getActionDataProvider(): array
    {
        return [
            'list'           => ['get', '/pears', 'defaultList', 'list'],
            'create'         => ['post', '/pears', 'defaultCreate', 'create'],
            'read'           => ['get', '/pears/1', 'defaultRead', 'read'],
            'update'         => ['patch', '/pears/1', 'defaultUpdate', 'update'],
            'delete'         => ['delete', '/pears/1', 'defaultDelete', 'delete'],
            'read:brands'    => ['get', '/pears/1/brands', 'defaultRelationRead', 'readBrands'],
            'reorder:brands' => ['patch', '/pears/1/brands', 'defaultRelationReorder', 'reorderBrands'],
        ];
    }

    /**
     * @param string $method
     * @param string $uri
     * @param string $expectedDefault
     * @param string $expectedActual
     *
     * @dataProvider getActionDataProvider
     */
    public function testGetAction(string $method, string $uri, string $expectedDefault, string $expectedActual): void
    {
        $request = Request::create($uri, $method);

        /** @var BaseController|MockObject $controller */
        $controller = $this->getMockForAbstractClass(BaseController::class);

        [$default, $actual] = $controller->getAction($request, []);

        $this->assertEquals($expectedDefault, $default);
        $this->assertEquals($expectedActual, $actual);
    }
}
