<?php

namespace Mathrix\Lumen\Tests\Traits;

use Exception;
use Laravel\Lumen\Testing\Concerns\MakesHttpRequests;
use Mathrix\Tests\OpenAPI\OpenAPITrait;

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
        echo "{$this->requestMethod} {$this->requestUri}\n";
        echo json_encode($this->getJsonResponseContent(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
