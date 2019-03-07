<?php

namespace Mathrix\Lumen\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Mathrix\Lumen\Bases\BaseController;

/**
 * Class ChecksController.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
class ChecksController extends BaseController
{
    /**
     * Allow to perform a check based on a Closure
     * @param \Closure $callback
     * @return array
     */
    private function check(\Closure $callback)
    {
        // Database test
        $start = microtime(true);
        $healthy = $callback();
        $latency = (microtime(true) - $start) * 1000;

        return [
            "healthy" => $healthy,
            "latency_ms" => round($latency, 2)
        ];
    }

    /**
     * GET /checks/health
     * Application health check.
     *
     * @return JsonResponse
     */
    public function health()
    {
        $data = [
            "mysql" => $this->check(function () {
                try {
                    /** @var \Illuminate\Database\MySqlConnection $connection */
                    $connection = DB::connection();
                    $pdo = $connection->getPdo();
                    return $pdo instanceof \PDO;
                } catch (\Exception $e) {
                    // @codeCoverageIgnoreStart
                    return false;
                    // @codeCoverageIgnoreEnd
                }
            })
        ];

        return new JsonResponse($data);
    }
}
