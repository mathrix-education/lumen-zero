<?php

namespace Mathrix\Lumen\Tests\REST;

/**
 * Trait RESTDeleteTrait.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
trait RESTDeleteTrait
{
    /**
     * Assert success for delete call.
     *
     * @param array $options Options of the request.
     */
    public function assertRestDeleteSuccess(array $options = []): void
    {
        $this->restDelete($options);

        // Assertions
        $this->assertNotInDatabase($this->table, ["id" => $this->requestModel->id]);
        $this->assertResponseOk();
        $this->assertOpenAPIResponse();
    }


    /**
     * Generic delete call.
     *
     * @param array $options Options of the request.
     */
    public function restDelete(array $options = []): void
    {
        $this->setRequestModel($options);

        $uri = "/{$this->baseUri}/{$this->requestModel->id}";

        $this->event("beforeRequest", "delete", $uri);
        $this->json("delete", $uri);
    }
}
