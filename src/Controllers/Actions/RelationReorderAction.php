<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Controllers\Actions;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Mathrix\Lumen\Zero\Controllers\Wrapper;
use Mathrix\Lumen\Zero\Exceptions\Http\Http400BadRequest;
use Mathrix\Lumen\Zero\Models\BaseModel;
use Mathrix\Lumen\Zero\Responses\DataResponse;
use Mathrix\Lumen\Zero\Responses\PaginationResponse;

/**
 * @method Builder query()
 */
trait RelationReorderAction
{
    /**
     * PATCH /{models}/{identifier}/{relation}
     *
     * @param Request    $request
     * @param int|string $identifier
     * @param string     $relation
     *
     * @return PaginationResponse
     *
     * @throws Http400BadRequest
     */
    final protected function defaultRelationReorder(Request $request, $identifier, string $relation): DataResponse
    {
        $wrapper = new Wrapper($request, $this->modelClass);

        /** @var BaseModel $model */
        $model = $this->query()
            ->where($wrapper->getKey(), '=', $identifier)
            ->firstOrFail();

        /** @var BelongsToMany $rel */
        $rel = $model->$relation();

        if (!($request instanceof BelongsToMany)) {
            throw new Http400BadRequest([], "{$this->modelClass}::$relation() is not orderable");
        }

        $relatedTable = $rel->getRelated()->getTable();
        $this->validate($request, ['*' => "distinct|exists:$relatedTable,{$model->getKeyName()}"]);

        $rel->sync($request->all());
        $rel->with($wrapper->getWith());

        $query = $rel->where($wrapper->getWheres())
            ->orderBy($wrapper->getOrderColumn(), $wrapper->getOrderDirection())
            ->limit($wrapper->getLimit())
            ->offset($wrapper->getOffset());

        return new PaginationResponse($query);
    }
}
