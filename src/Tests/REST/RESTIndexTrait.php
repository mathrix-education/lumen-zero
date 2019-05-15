<?php

namespace Mathrix\Lumen\Zero\Tests\REST;

/**
 * Trait RESTIndexTrait.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
trait RESTIndexTrait
{
    /**
     * Assert success for generic index call, paginated.
     *
     * @param array $options Options of the request.
     */
    public function assertRestIndexSuccess(array $options = []): void
    {
        [$page, $perPage] = $this->getPaginationParameters($options);

        $this->restIndex($options);

        // Assertions
        $this->assertResponseOk();
        $this->assertEquals($page, $this->getJsonResponseValue("page"));
        $this->assertEquals($perPage, $this->getJsonResponseValue("per_page"));
        $this->assertOpenAPIResponse();
    }


    /**
     * Generic index call, paginated.
     *
     * @param array $options Options of the request.
     */
    public function restIndex(array $options = []): void
    {
        [$page, $perPage] = $this->getPaginationParameters($options);
        $uri = "/{$this->baseUri}/$page/$perPage";

        $this->event("before.request", "get", $uri);
        $this->json("get", $uri);
    }
}
