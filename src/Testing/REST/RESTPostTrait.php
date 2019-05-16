<?php

namespace Mathrix\Lumen\Zero\Testing\REST;

/**
 * Class RESTPostTrait.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
trait RESTPostTrait
{
    /**
     * Assert success for generic post call.
     *
     * @param array|callable $before Array or closure to override data before it is sent.
     * @param array|callable $after Array or closure to override data after it is sent.
     * @param array $options Options of the request.
     */
    public function assertRestPostSuccess($before = null, $after = null, array $options = []): void
    {
        $this->restPost($before, $after, $options);

        // Assertions
        $this->assertResponseOk();
        $this->assertInDatabase($this->table, $this->afterRequestData);
        $this->assertOpenAPIResponse();
    }


    /**
     * Generic post call.
     *
     * @param array|callable $before Array or closure to override data before it is sent.
     * @param array|callable $after Array or closure to override data after it is sent.
     * @param array $options Options of the request.
     */
    public function restPost($before = null, $after = null, array $options = []): void
    {
        $this->beforeRequestData = $this->override(
            $this->getFactoryBuilder($options)->raw(),
            $before
        );

        $uri = "/{$this->baseUri}";

        $this->event("before.request", "post", $uri);
        $this->json("post", $uri, $this->beforeRequestData);

        $this->afterRequestData = $this->override(
            $this->beforeRequestData,
            $after
        );
    }

}
