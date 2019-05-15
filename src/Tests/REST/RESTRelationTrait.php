<?php

namespace Mathrix\Lumen\Zero\Tests\REST;

/**
 * Trait RESTRelationTraitTrait.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
trait RESTRelationTrait
{
    /**
     * Assert success for generic by call.
     *
     * @param string $relation The relation.
     * @param array $options Options of the request.
     */
    public function assertRestRelationSuccess(string $relation, array $options = []): void
    {
        $this->restRelation($relation, $options);
        $uri = $this->getOpenAPIUri($this->requestMethod, $this->requestUri);

        $this->assertResponseOk();
        $this->assertOpenAPIResponse($uri);
    }


    /**
     * Generic relation call.
     *
     * @param string $relation The relation.
     * @param array $options Options of the request.
     */
    public function restRelation(string $relation, array $options = []): void
    {
        $conditions = $options["conditions"] ?? [];
        $this->requestModel = $this->modelClass::query()
            ->inRandomOrder()
            ->has($relation)
            ->where($conditions)
            ->firstOrFail();

        [$page, $perPage] = $this->getPaginationParameters($options);
        $uri = "/{$this->baseUri}/{$this->requestModel->id}/$relation/$page/$perPage";

        $this->event("before.request", "get", $uri);
        $this->json("get", $uri);
    }
}
