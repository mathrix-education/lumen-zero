<?php

namespace Mathrix\Lumen\Tests\Traits;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
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
     * Make a database builder for database existence tests.
     * @param string $table The table where the dataset should be located
     * @param BaseModel|array $data The data
     * @param null $onConnection The database connection
     * @return Builder
     */
    protected function makeBuilder(string $table, $data, $onConnection = null)
    {
        $builder = DB::connection($onConnection)->table($table);

        foreach ($data as $key => $value) {
            if ($value instanceof Carbon) {
                $builder = $builder->where($key, "=", $value->format("Y-m-d H:i:s"));
            } else if (is_bool($value) && $value) {
                $builder = $builder->where($key, "=", 1);
            } else if (is_bool($value) && !$value) {
                $builder = $builder->where($key, "=", 0);
            } else if (is_array($value)) {
                $builder = $builder->whereJsonContains($key, $value)
                    ->whereJsonLength($key, count($value));
            } else if (is_null($value)) {
                $builder = $builder->whereNull($key);
            } else {
                $builder = $builder->where($key, "=", $value);
            }
        }

        return $builder;
    }


    /**
     * Assert that dataset exists in the database.
     * @param string $table The table where the dataset should be located
     * @param BaseModel|array $data The data
     * @param null $onConnection The database connection
     * @return self
     */
    public function assertInDatabase(string $table, $data, $onConnection = null)
    {
        $count = $this->makeBuilder($table, $data, $onConnection)->count();
        $this->assertGreaterThan(
            0,
            $count,
            "Unable to find row in database table [$table] that matched attributes.\nSubmitted data:\n"
            . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        return $this;
    }


    /**
     * Assert that dataset should not exist in the database.
     * @param string $table The table where the dataset should be located
     * @param BaseModel|array $data The data
     * @param null $onConnection The database connection
     * @return self
     */
    public function assertNotInDatabase(string $table, $data, $onConnection = null)
    {
        $count = $this->makeBuilder($table, $data, $onConnection)->count();
        $this->assertEquals(
            0,
            $count,
            "Found $count unexpected row in database table [$table] that matched attributes.\nSubmitted data:\n"
            . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        return $this;
    }
}
