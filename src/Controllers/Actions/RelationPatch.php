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
     * @param string $key The model key.
     * @param string|int $identifier The model identifier.
     * @param string $relation The model relation.
     *
     * @return PaginationJsonResponse
     * @throws Http400BadRequestException
     */
    public function relationPatch(Request $request, string $key, $identifier, string $relation): PaginationJsonResponse
    {
        /** @var BaseModel $model */
        $model = $this->modelClass::findByOrFail($key, $identifier);

        $ability = $this->getAbility("patch", "relation", $key, $relation);
        $this->canOrFail($request, $ability, $model);

        $relatedTable = $model->{$relation}()->getRelated()->getTable();

        $this->validate($request, [
            "*" => "distinct|exists:$relatedTable,$key"
        ]);

        $model->{$relation}()->sync($request->all());

        $query = $model->{$relation}()
            ->with($this->with["rel:patch:$relation"] ?? []);

        return new PaginationJsonResponse($query);
    }
}
