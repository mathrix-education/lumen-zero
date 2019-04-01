<?php

namespace Mathrix\Lumen\Tests\REST;

use Illuminate\Support\Str;
use Mathrix\Lumen\Bases\BaseModel;

/**
 * Class RestByTrait.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
trait RESTByTrait
{
    /**
     * Assert success for generic by call.
     *
     * @param BaseModel|string $modelClass The related model class
     * @param array $options Options of the request.
     */
    public function assertRestBySuccess(string $modelClass, array $options = []): void
    {
        $this->restBy($modelClass, $options);

        $this->assertResponseOk();
        $this->assertIsPaginatedResponse();
    }


    /**
     * Generic by call.
     *
     * @param BaseModel|string $modelClass The related model class
     * @param array $options Options of the request.
     */
    public function restBy(string $modelClass, array $options = []): void
    {
        $conditions = $options["conditions"] ?? [];
        $this->requestModel = $modelClass::random($conditions);
        $relatedModelUri = Str::singular($modelClass::getTableName());
        [$page, $perPage] = $this->getPaginationParameters($options);
        $uri = "/{$this->baseUri}/by-$relatedModelUri/{$this->requestModel->id}/$page/$perPage";

        $this->autoMockScope("get", $uri);
        $this->json("get", $uri);
    }
}
