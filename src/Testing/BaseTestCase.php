<?php

namespace Mathrix\Lumen\Zero\Testing;

use Laravel\Lumen\Testing\TestCase;

/**
 * Class BaseTestCase.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 2.0.0
 */
abstract class BaseTestCase extends TestCase
{
    protected static $bootedTraits = [];


    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::bootTraits();
    }


    /**
     * Boot all of the bootable traits on the model.
     *
     * @return void
     */
    protected static function bootTraits()
    {
        $class = static::class;

        foreach (class_uses_recursive($class) as $trait) {
            $method = 'boot' . class_basename($trait);

            if (method_exists($class, $method) && !in_array($trait, self::$bootedTraits)) {
                forward_static_call([$class, $method]);
                static::$bootedTraits[] = $trait;
            }
        }
    }


    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        static::$bootedTraits = []; // Clear booted traits
    }
}
