<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Testing;

use ReflectionClass;
use ReflectionException;
use function get_class;

/**
 * Allow protected and private method testing.
 */
trait Reflector
{
    /**
     * Call protected/private method of a class.
     *
     * @param object &$object      Instantiated object that we will set property on.
     * @param string $propertyName The property name to set.
     * @param mixed  $value        The property value to set.
     *
     * @throws ReflectionException
     */
    public function reflectSet(&$object, string $propertyName, $value): void
    {
        $subject  = new ReflectionClass($object);
        $property = $subject->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     *
     * @throws ReflectionException
     */
    public function reflectInvoke(&$object, string $methodName, array $parameters = [])
    {
        $subject = new ReflectionClass(get_class($object));
        $method  = $subject->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
