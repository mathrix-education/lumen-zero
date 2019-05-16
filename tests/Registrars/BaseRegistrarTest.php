<?php

namespace Mathrix\Lumen\Zero\Registrars;

use Illuminate\Support\Collection;
use Laravel\Lumen\Routing\Router;
use Mathrix\Lumen\Zero\Testing\ModelMockFactory;
use Mathrix\Lumen\Zero\Tests\Traits\ReflectorTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * Class BaseRegistrarTest.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 2.0.0
 */
class BaseRegistrarTest extends TestCase
{
    use ReflectorTrait;


    public function makeRESTRoutesDataProvider()
    {
        return Collection::make([
            // Standard
            "std:index" => ["get", "fruits"],
            "std:post" => ["post", "fruits"],
            "std:get" => ["get", "fruits/{fruitId}"],
            "std:patch" => ["patch", "fruits/{fruitId}"],
            "std:delete" => ["delete", "fruits/{fruitId}"],
            "std:get:slug" => ["get", "fruits/slug/{fruitSlug}"],
            "std:patch:slug" => ["patch", "fruits/slug/{fruitSlug}"],

            // Relations
            "rel:get:brands" => ["get", "fruits/{fruitId}/brands"],
            "rel:patch:brands" => ["patch", "fruits/{fruitId}/brands"],
            "rel:get:slug:brands" => ["get", "fruits/slug/{fruitSlug}/brands"],
            "rel:patch:slug:brands" => ["patch", "fruits/slug/{fruitSlug}/brands"],
        ])->mapWithKeys(function ($value, string $key) {
            $value[] = $key;

            return [$key => $value];
        });
    }


    /**
     * @param $expectedMethod
     * @param $expectedUri
     * @param $routeKey
     *
     * @throws ReflectionException
     * @dataProvider makeRESTRoutesDataProvider
     * @covers       \Mathrix\Lumen\Zero\Registrars\BaseRegistrar::makeRESTRoute
     */
    public function testMakeRESTRoute($expectedMethod, $expectedUri, $routeKey): void
    {
        $modelClass = "App\\Models\\Fruit";

        if (!class_exists($modelClass)) {
            ModelMockFactory::make()
                ->setName("Fruit")
                ->setMethod("public", "getKeyName", "id")
                ->compile()
                ->exec();
        }

        /** @var MockObject|Router $registrar */
        $router = $this->createMock(Router::class);

        /** @var MockObject|BaseRegistrar $registrar */
        $registrar = $this->getMockForAbstractClass(
            BaseRegistrar::class,
            [$router],
            "FruitsController",
            true,
            true,
            true,
            ["post", "get", "patch", "delete"]
        );

        $registrar->expects($this->once())
            ->method($expectedMethod)
            ->with($expectedUri);

        $registrar->makeRESTRoute($routeKey);
    }
}
