<?php

namespace Mathrix\Lumen\Zero\Controllers\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mathrix\Lumen\Zero\Exceptions\Http\Http400BadRequestException;
use Mathrix\Lumen\Zero\Models\BaseModel;
use Mathrix\Lumen\Zero\Responses\PaginationJsonResponse;
use Mathrix\Lumen\Zero\Responses\SuccessJsonResponse;

/**
 * Trait RelationGet.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 2.0.0
 */
trait RelationGet
{
    /**
     * GET /models/{identifier}/{relation} (if key is "id")
     * GET /models/{key}/{identifier}/{relation}
     *
     * @param Request $request The HTTP request.
     * @param string $key The model key.
     * @param string|int $identifier The model identifier.
     * @param string $relation The model relation.
     *
     * @return PaginationJsonResponse
     * @throws Http400BadRequestException
     */
    public function relationGet(Request $request, string $key, $identifier, string $relation): SuccessJsonResponse
    {
        /** @var BaseModel $model */
        $model = $this->modelClass::findByOrFail($key, $identifier);

        $ability = $this->getAbility("get", "relation", $key, $relation);
        $this->canOrFail($request, $ability, $model);

        // Make the Eloquent query
        $query = $model->{$relation}()
            ->with($this->with["rel:get:$relation"] ?? []);

        return new PaginationJsonResponse($query);
    }
}
