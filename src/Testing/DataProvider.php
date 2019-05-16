<?php

namespace Mathrix\Lumen\Zero\Testing;

/**
 * Class DataProvider.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 2.0.0
 */
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
