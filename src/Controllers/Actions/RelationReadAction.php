<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Controllers\Actions;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Mathrix\Lumen\Zero\Controllers\QueryExtractor;
use Mathrix\Lumen\Zero\Models\BaseModel;
use Mathrix\Lumen\Zero\Responses\DataResponse;
use Mathrix\Lumen\Zero\Responses\PaginationResponse;

/**
 * @property string $modelClass
 * @method Builder query()
 */
trait RelationReadAction
{
    /**
     * GET /{models}/{identifier}/{relation}
     *
     * @param Request    $request
     * @param int|string $identifier
     * @param string     $relation
     *
     * @return DataResponse
     */
    final public function defaultRelationRead(Request $request, $identifier, string $relation)
    {
        $wrapper = new QueryExtractor($request, $this->modelClass);

        /** @var BaseModel $model */
        $model = $this->query()
            ->where($wrapper->getKey(), '=', $identifier)
            ->firstOrFail();

        /** @var Relation $rel */
        $rel = $model->$relation();
        $rel->with($wrapper->getWith());

        if ($rel instanceof BelongsTo) {
            return new DataResponse($rel);
        }

        $query = $rel->where($wrapper->getWheres())
            ->orderBy($wrapper->getOrderColumn(), $wrapper->getOrderDirection())
            ->limit($wrapper->getLimit())
            ->offset($wrapper->getOffset());

        return new PaginationResponse($query);
    }
}
