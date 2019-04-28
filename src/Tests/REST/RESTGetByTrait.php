<?php

namespace Mathrix\Lumen\Tests\REST;

/**
 * Class RESTGetByTrait.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
trait RESTGetByTrait
{
    /**
     * Assert success for generic get by field call.
     *
     * @param string $field The field which will be used.
     * @param array $options Options of the request.
     */
    public function assertRestGetBySuccess(string $field, array $options = []): void
    {
        $this->restGetBy($field, $options);

        // Assertions
        $this->assertResponseOk();
        $this->assertEquals($this->requestModel->{$field}, $this->getJsonResponseValue($field));
        $this->assertOpenAPIResponse();
    }


    /**
     * Generic get call.
     *
     * @param string $field The field which will be used.
     * @param array $options Options of the request.
     */
    public function restGetBy(string $field, array $options = []): void
    {
        $this->setRequestModel($options);

        $uri = "/{$this->baseUri}/$field/{$this->requestModel->{$field}}";

        $this->event("before.request", "get", $uri);
        $this->json("get", $uri);
    }
}
