<?php

namespace Mathrix\Lumen\Tests\Traits;

use Illuminate\Support\Arr;
use Laravel\Lumen\Testing\Concerns\MakesHttpRequests;
use stdClass;

/**
 * Trait JsonResponseTrait.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 *
 * @mixin MakesHttpRequests
 */
trait JsonResponseTrait
{
    /**
     * Get the JsonResponse content.
     * @return stdClass
     */
    public function getJsonResponseContent(): stdClass
    {
        return json_decode($this->response->getContent());
    }


    /**
     * Get a JsonResponse value by key.
     * @param string $key The key in dot-notation style.
     * @return mixed|null
     */
    public function getJsonResponseValue(string $key)
    {
        $responseDataFlatten = Arr::dot((array)$this->getJsonResponseContent());

        if (isset($responseDataFlatten[$key])) {
            return $responseDataFlatten[$key];
        } else {
            return null;
        }
    }
}
