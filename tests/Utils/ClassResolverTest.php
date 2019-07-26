<?php

namespace Mathrix\Lumen\Zero\Utils;

use Mathrix\Lumen\Zero\Testing\DataProvider;
use Mathrix\Lumen\Zero\Testing\ModelMockFactory;
use PHPUnit\Framework\TestCase;


/**
 * Class ClassResolverTest.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
class ClassResolverTest extends TestCase
{
    public function getModelClassDataProvider(): array
    {
        return DataProvider::makeDataProvider([
            "App\\Models\\Cake" => "App\\Controllers\\CakesController",
            "App\\Models\\Card" => "App\\Observers\\CardsController",
            "App\\Models\\Blueberry" => "App\\Policies\\BlueberryPolicy",
            "App\\Models\\Banana" => "Tests\\API\\BananasTest",
            "App\\Models\\User" => "Tests\\API\\UsersLoginTest",
            "App\\Models\\LineItem" => "App\\Observers\\LineItemObserver"
        ]);
    }


    /**
     * @param string $expected
     * @param string $caller
     *
     * @dataProvider getModelClassDataProvider
     * @covers       \Mathrix\Lumen\Zero\Utils\ClassResolver::getModelClass
     */
    public function testGetModelClass(string $expected, string $caller)
    {
        ModelMockFactory::make()
            ->setFullyQualifiedName($expected)
            ->compile()
            ->exec();

        $this->assertEquals($expected, ClassResolver::getModelClass($caller));
    }
}
