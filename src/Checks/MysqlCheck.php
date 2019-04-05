<?php

namespace Mathrix\Lumen\Checks;

use Illuminate\Database\MySqlConnection;
use Illuminate\Support\Facades\DB;
use PDO;

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
        /** @var MySqlConnection $connection */
        $connection = DB::connection();
        $pdo = $connection->getPdo();
        $result = $pdo instanceof PDO;

        if ($result) {
            return "healthy";
        } else {
            return "unhealthy";
        }
    }
}
