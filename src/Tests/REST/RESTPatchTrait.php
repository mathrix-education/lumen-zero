<?php

namespace Mathrix\Lumen\Tests\REST;

use Mathrix\Lumen\Utils\ClassResolver;

/**
 * Trait RESTPatchTrait.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
trait RESTPatchTrait
{
    /**
     * Assert success for generic patch call.
     *
     * @param array|callable $before Array or closure to override data before it is sent.
     * @param array|callable $after Array or closure to override data after it is sent.
     * @param array $options Options of the request.
     */
    public function assertRestPatchSuccess($before = null, $after = null, array $options = []): void
    {
        $this->restPatch($before, $after, $options);

        // Assertions
        $this->assertResponseOk();
        $this->assertInDatabase($this->table, $this->afterRequestData);
        $this->assertJsonResponseMatchesJsonSchema(ClassResolver::baseClassName($this->modelClass));
    }


    /**
     * Generic patch call.
     *
     * @param array|callable $before Array or closure to override data before it is sent.
     * @param array|callable $after Array or closure to override data after it is sent.
     * @param array $options Options of the request.
     */
    public function restPatch($before = null, $after = null, array $options = []): void
    {
        $this->setRequestModel($options);

        $this->beforeRequestData = $this->override(
            $this->getFactoryBuilder($options)->raw(),
            $before
        );

        $uri = "/{$this->baseUri}/{$this->requestModel->id}";

        $this->autoMockScope("patch", $uri);
        $this->json("patch", $uri, $this->beforeRequestData);

        $this->afterRequestData = $this->override(
            $this->beforeRequestData,
            $after
        );
    }
}
