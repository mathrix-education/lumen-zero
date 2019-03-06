<?php

namespace Mathrix\Lumen\Tests\Traits;

use Carbon\Carbon;
use Mathrix\Lumen\Bases\BaseModel;

/**
 * Trait DatabaseTrait.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
trait DatabaseTrait
{
    /**
     * Cast data to database format
     * @param BaseModel|array $data
     * @return array
     */
    protected function castToDatabase($data): array
    {
        if ($data instanceof BaseModel) {
            $data = $data->toArray();
        }

        foreach ($data as $k => $v) {
            if ($v instanceof Carbon) {
                $data[$k] = $v->format("Y-m-d H:i:s");
            } elseif (is_array($v)) {
                $data[$k] = json_encode($v);
            } elseif (is_bool($v) && $v) {
                $data[$k] = 1;
            } elseif (is_bool($v) && !$v) {
                $data[$k] = 0;
            }
        }

        return $data;
    }

    /**
     * Assert that dataset exists in the database.
     * @param string $table The table where the dataset should be located
     * @param BaseModel|array $data The data
     * @param null $onConnection The database connection
     */
    public function assertInDatabase(string $table, $data, $onConnection = null)
    {
        $this->seeInDatabase($table, $this->castToDatabase($data), $onConnection);
    }

    /**
     * Assert that dataset should not exist in the database.
     * @param string $table The table where the dataset should be located
     * @param BaseModel|array $data The data
     * @param null $onConnection The database connection
     */
    public function assertNotInDatabase(string $table, $data, $onConnection = null)
    {
        $this->missingFromDatabase($table, $this->castToDatabase($data), $onConnection);
    }
}
