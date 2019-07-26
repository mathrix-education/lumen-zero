<?php

namespace Mathrix\Lumen\Zero\Testing\Traits;

/**
 * Trait RESTAutoTestTrait.
 * Automatically test using the self::$testKeys array.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA
 * @since 2.3.1
 *
 * @mixin RESTTrait
 */
trait RESTAutoTestTrait
{
    /**
     * Declare the test keys for "standard" REST tests.
     * @return array
     */
    public function restDataProvider(): array
    {
        $data = array_map(function (string $key) {
            return [$key];
        }, $this->testKeys);

        return array_combine($this->testKeys, $data);
    }


    /**
     * Test the "standard" REST using test keys.
     * @dataProvider restDataProvider
     * @param string $key
     */
    public function testREST(string $key): void
    {
        $this->makeRESTJsonRequest($key);
    }
}
