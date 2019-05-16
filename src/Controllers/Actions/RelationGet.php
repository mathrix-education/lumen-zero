<?php

namespace Mathrix\Lumen\Zero\Controllers\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mathrix\Lumen\Zero\Exceptions\Http\Http400BadRequestException;
use Mathrix\Lumen\Zero\Models\BaseModel;
use Mathrix\Lumen\Zero\Responses\PaginationJsonResponse;

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
     * @param string $relation The model relation.
     * @param string $key The model key.
     * @param int $identifier The model identifier.
     *
     * @return PaginationJsonResponse
     * @throws Http400BadRequestException
     */
    public function relationGet(Request $request, string $key, int $identifier, string $relation): JsonResponse
    {
        /** @var BaseModel $model */
        $model = $this->modelClass::findByOrFail($key, $identifier);

        $ability = $this->getAbility("get", "relation", $key, $relation);
        $this->canOrFail($request, $ability, $model);
        $related = $model->{$relation}();

        return new PaginationJsonResponse($related);
    }
}
