<?php

namespace Mathrix\Lumen\Zero\Registrars;

use Illuminate\Support\Collection;
use Laravel\Lumen\Routing\Router;
use Mathrix\Lumen\Zero\Testing\ModelMockFactory;
use Mathrix\Lumen\Zero\Testing\Traits\ReflectorTrait;
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
            "std:index" => ["get", "pears"],
            "std:post" => ["post", "pears"],
            "std:get" => ["get", "pears/{pearId}"],
            "std:patch" => ["patch", "pears/{pearId}"],
            "std:delete" => ["delete", "pears/{pearId}"],
            "std:get:slug" => ["get", "pears/slug/{pearSlug}"],
            "std:patch:slug" => ["patch", "pears/slug/{pearSlug}"],

            // Relations
            "rel:get:brands" => ["get", "pears/{pearId}/brands"],
            "rel:patch:brands" => ["patch", "pears/{pearId}/brands"],
            "rel:get:slug:brands" => ["get", "pears/slug/{pearSlug}/brands"],
            "rel:patch:slug:brands" => ["patch", "pears/slug/{pearSlug}/brands"],
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
        $modelClass = "App\\Models\\Pear";

        if (!class_exists($modelClass)) {
            ModelMockFactory::make()
                ->setName("Pear")
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
            "PearsController",
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
