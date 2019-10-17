<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Testing;

use Laravel\Lumen\Testing\TestCase;
use function class_basename;
use function class_uses_recursive;
use function forward_static_call;
use function in_array;
use function method_exists;

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

            if (!method_exists($class, $method) || in_array($trait, self::$bootedTraits)) {
                continue;
            }

            forward_static_call([$class, $method]);
            static::$bootedTraits[] = $trait;
        }
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        static::$bootedTraits = []; // Clear booted traits
    }
}
