<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Testing;

use Illuminate\Http\Response;
use Laravel\Lumen\Application;
use Laravel\Lumen\Http\Request;
use Laravel\Lumen\Testing\Concerns\MakesHttpRequests;
use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;
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
    public function debug(): void
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

        $data    = json_decode($this->response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $content = json_encode($data, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE, 512);

        echo "HTTP/$status $method $uri\n$content";
    }
}
