<?php

namespace Mathrix\Lumen\Tests\Traits;

use Exception;
use Laravel\Lumen\Testing\Concerns\MakesHttpRequests;
use Mathrix\Lumen\Tests\OpenAPI\OpenAPITrait;

/**
 * Trait DebugTrait.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 *
 * @mixin JsonResponseTrait
 * @mixin MakesHttpRequests
 * @mixin OpenAPITrait
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
        if ($this->response !== null) {
            $status = $this->response->getStatusCode();
            $method = strtoupper($this->requestMethod);
            $uri = $this->requestUri;
            $content = json_encode(json_decode($this->response->getContent()),
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

            echo "HTTP/$status $method $uri\n$content";
        }
    }
}
