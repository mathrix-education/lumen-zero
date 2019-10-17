<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Testing;

use function array_merge;
use function is_array;

class DataProvider
{
    /**
     * Make data provider for given input data (key is the expected data, value is the args).
     *
     * @param array $input
     *
     * @return array
     */
    public static function makeDataProvider(array $input): array
    {
        $data = [];

        foreach ($input as $expected => $args) {
            if (!is_array($args)) {
                $args = [$args];
            }

            $data[$expected] = array_merge([$expected], $args);
        }

        return $data;
    }
}
