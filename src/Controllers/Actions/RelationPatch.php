<?php

namespace Mathrix\Lumen\Zero\Controllers\Actions;

use Illuminate\Http\Request;
use Mathrix\Lumen\Zero\Exceptions\Http\Http400BadRequestException;
use Mathrix\Lumen\Zero\Models\BaseModel;
use Mathrix\Lumen\Zero\Responses\PaginationJsonResponse;

/**
 * Trait RelationPatch.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.5.0
 */
trait RelationPatch
{
    /**
     * PATCH /models/{identifier}/{relation} (if key is "id")
     * PATCH /models/{key}/{identifier}/{relation}
     *
     * @param Request $request The HTTP request.
     * @param string $relation The model relation.
     * @param string $key The model key.
     * @param int $identifier The model identifier.
     *
     * @return PaginationJsonResponse
     * @throws Http400BadRequestException
     */
    public function relationPatch(Request $request, string $key, int $identifier, string $relation): PaginationJsonResponse
    {
        /** @var BaseModel $model */
        $model = $this->modelClass::findByOrFail($key, $identifier);

        $ability = $this->getAbility("patch", "relation", $key, $relation);
        $this->canOrFail($request, $ability, $model);

        $this->validate($request, [
            "*" => "distinct|exists:{$this->modelClass::getTableName()},$key"
        ]);

        $model->{$relation}()->sync($request->all());

        return new PaginationJsonResponse($model->{$relation}());
    }
}
