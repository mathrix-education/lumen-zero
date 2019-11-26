<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Testing\Traits;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Mathrix\Lumen\Zero\Models\BaseModel;
use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;
use function count;
use function is_array;
use function is_bool;
use function json_encode;

/**
 * Trait DatabaseTrait.
 */
trait DatabaseAssertions
{
    /**
     * Make a database builder for database existence tests.
     *
     * @param string          $table        The table where the dataset should be located
     * @param BaseModel|array $data         The data
     * @param null            $onConnection The database connection
     *
     * @return Builder
     */
    protected function makeBuilder(string $table, $data, $onConnection = null)
    {
        $builder = DB::connection($onConnection)->table($table);

        foreach ($data as $key => $value) {
            if ($value instanceof Carbon) {
                $builder = $builder->where($key, '=', $value->format('Y-m-d H:i:s'));
            } elseif (is_bool($value) && $value) {
                $builder = $builder->where($key, '=', 1);
            } elseif (is_bool($value) && !$value) {
                $builder = $builder->where($key, '=', 0);
            } elseif (is_array($value)) {
                $builder = $builder->whereJsonContains($key, $value)
                    ->whereJsonLength($key, count($value));
            } elseif ($value === null) {
                $builder = $builder->whereNull($key);
            } else {
                $builder = $builder->where($key, '=', $value);
            }
        }

        return $builder;
    }

    /**
     * Assert that a dataset exists in the database.
     *
     * @param string          $table        The table where the dataset should be located
     * @param BaseModel|array $data         The data
     * @param null            $onConnection The database connection
     *
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
     *
     * @param string          $table        The table where the dataset should be located
     * @param BaseModel|array $data         The data
     * @param null            $onConnection The database connection
     *
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
