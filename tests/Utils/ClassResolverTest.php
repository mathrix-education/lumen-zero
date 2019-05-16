<?php

namespace Mathrix\Lumen\Zero\Utils;

use Mathrix\Lumen\Zero\Testing\DataProvider;
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
            "App\\Models\\Apple" => "App\\Policies\\ApplePolicy",
            "App\\Models\\Banana" => "Tests\\API\\BananasTest",
            "App\\Models\\User" => "Tests\\API\\UsersLoginTest"
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
        $this->assertEquals($expected, ClassResolver::getModelClass($caller, true));
    }
}
