<?php

namespace Mathrix\Lumen\Checks;

use Illuminate\Support\Facades\DB;

/**
 * Class MysqlCheck.
 * Check if MySQL connection works as expected.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
class MysqlCheck extends BaseCheck
{
    protected function run(): string
    {
        /** @var \Illuminate\Database\MySqlConnection $connection */
        $connection = DB::connection();
        $pdo = $connection->getPdo();
        return $pdo instanceof \PDO;
    }
}
