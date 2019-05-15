<?php

use Mathrix\Lumen\Zero\Utils\ClassResolver;
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
    /**
     * @covers \Mathrix\Lumen\Utils\ClassResolver::getModelClass
     */
    public function testGetModelClass()
    {
        $samples = [
            ["Cake", false, null],
            ["Cake", true, "App\\Models\\Cake"],
            ["App\\Controllers\\CakesController", true, "App\\Models\\Cake"],
            ["Tests\\API\\CakesTest", true, "App\\Models\\Cake"],
            [$this, true, "App\\Models\\ClassResolver"]
        ];

        foreach ($samples as $sample) {
            [$class, $force, $expected] = $sample;
            $this->assertEquals($expected, ClassResolver::getModelClass($class, $force));
        }
    }
}
