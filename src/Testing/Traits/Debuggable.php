<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Testing\Traits;

use Illuminate\Http\Response;
use Laravel\Lumen\Application;
use Laravel\Lumen\Http\Request;
use Laravel\Lumen\Testing\Concerns\MakesHttpRequests;
use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_UNICODE;
use function json_decode;
use function json_encode;
use function strtoupper;

/**
 * Trait DebugTrait.
 *
 * @property Application $app
 * @mixin MakesHttpRequests
 */
trait Debuggable
{
    /**
     * Debug the response.
     */
    public function debug()
    {
        /** @var Request $request */
        $request = $this->app['request'] ?? $this->request ?? null;
        /** @var Response $response */
        $response = $this->response ?? null;

        if ($response === null || $request === null) {
            return;
        }

        $status = $this->response->getStatusCode();
        $method = strtoupper($request->getMethod());
        $uri    = $request->getUri();

        $content = json_encode(
            json_decode($this->response->getContent()),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );

        echo "HTTP/$status $method $uri\n$content";
    }
}
