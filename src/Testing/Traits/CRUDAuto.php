<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Testing\Traits;

use Mathrix\Lumen\Zero\Exceptions\InvalidArgument;
use function array_combine;
use function array_map;

/**
 * Trait RESTAutoTestTrait.
 * Automatically test using the self::$testKeys array.
 *
 * @mixin CRUD
 */
trait CRUDAuto
{
    /**
     * Declare the test keys for "standard" REST tests.
     *
     * @return array
     */
    public function CRUDDataProvider(): array
    {
        $data = array_map(static function (string $key) {
            return [$key];
        }, $this->testKeys);

        return array_combine($this->testKeys, $data);
    }

    /**
     * Test the "standard" REST using test keys.
     *
     * @param string $key
     *
     * @throws InvalidArgument
     *
     * @dataProvider CRUDDataProvider$
     */
    public function testCRUD(string $key): void
    {
        $this->makeRequest($key);
    }
}
