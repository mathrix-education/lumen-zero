<?php

namespace Mathrix\Lumen\Tests\REST;

/**
 * Trait RESTGetTrait.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
trait RESTGetTrait
{
    /**
     * Generic get call.
     *
     * @param array $options Options of the request.
     */
    public function restGet(array $options = []): void
    {
        $this->setRequestModel($options);

        $uri = "/{$this->baseUri}/{$this->requestModel->id}";

        $this->autoMockScope("get", $uri);
        $this->json("get", $uri);
    }


    /**
     * Assert success for generic get call.
     *
     * @param array $options Options of the request.
     */
    public function assertRestGetSuccess(array $options = []): void
    {
        $this->restGet($options);

        // Assertions
        $this->assertResponseOk();
        $this->assertEquals($this->requestModel->id, $this->getJsonResponseValue("id"));
        $this->assertResponseMatchesSchema($this->modelName);
    }

}
