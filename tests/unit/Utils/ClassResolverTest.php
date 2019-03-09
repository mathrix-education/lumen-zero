<?php

use Mathrix\Lumen\Utils\ClassResolver;


/**
 * Class ClassResolverTest.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
class ClassResolverTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers \Mathrix\Lumen\Utils\ClassResolver::getModelClass
     */
    public function testGetModelClass()
    {
        $this->assertEquals("App\\Models\\Test", ClassResolver::getModelClass("Test"));
    }


    /**
     * @covers \Mathrix\Lumen\Utils\ClassResolver::getModelClassFrom
     */
    public function testGetModelClassFrom()
    {
        $this->assertEquals(
            "App\\Models\\Test",
            ClassResolver::getModelClassFrom("Aloahs", "App\\Aloahs\\TestAloahs")
        );
    }
}
