<?php

namespace Mathrix\Lumen\Zero\Tests\Traits;

use ReflectionClass;
use ReflectionException;

/**
 * Trait MethodInvokerTrait.
 * Allow protected and private method testing.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 2.0.0
 */
trait ReflectorTrait
{
    /**
     * Call protected/private method of a class.
     *
     * @param object &$object Instantiated object that we will set property on.
     * @param string $propertyName The property name to set.
     * @param mixed $value The property value to set.
     *
     * @throws ReflectionException
     */
    public function set(&$object, string $propertyName, $value): void
    {
        $subject = new ReflectionClass($object);
        $property = $subject->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }


    /**
     * Call protected/private method of a class.
     *
     * @param object &$object Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     * @throws ReflectionException
     */
    public function invoke(&$object, string $methodName, array $parameters = [])
    {
        $subject = new ReflectionClass(get_class($object));
        $method = $subject->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
