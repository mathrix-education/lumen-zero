<?php

namespace Mathrix\Lumen\Zero\Testing\Traits;

use Exception;
use Illuminate\Http\Response;
use Laravel\Lumen\Application;
use Laravel\Lumen\Http\Request;
use Laravel\Lumen\Testing\Concerns\MakesHttpRequests;

/**
 * Trait DebugTrait.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 *
 * @property Application $app
 * @mixin MakesHttpRequests
 */
trait DebugTrait
{
    /**
     * Debug the response.
     *
     * @throws Exception
     */
    public function debug()
    {
        /** @var Request $request */
        $request = $this->app["request"] ?? $this->request ?? null;
        /** @var Response $response */
        $response = $this->response ?? null;

        if ($response !== null && $request !== null) {
            $status = $this->response->getStatusCode();
            $method = strtoupper($request->getMethod());
            $uri = $request->getUri();

            $content = json_encode(json_decode($this->response->getContent()),
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

            echo "HTTP/$status $method $uri\n$content";
        }
    }
}
