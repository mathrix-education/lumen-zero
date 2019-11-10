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
use function collect;

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
     * @param string     $column
     *
     * @return PaginationResponse
     *
     * @throws Http400BadRequest
     */
    final public function defaultRelationReorder(
        Request $request,
        $identifier,
        string $relation,
        string $column = 'order'
    ): DataResponse {
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

        if ($column !== null) {
            $sync = collect($request->all())
                ->mapWithKeys(static function (int $itemId, int $order) {
                    return [$itemId => ['order' => $order]];
                })
                ->toArray();
        } else {
            $sync = $request->all();
        }

        $rel->sync($sync);
        $rel->with($wrapper->getWith());

        $query = $rel->where($wrapper->getWheres())
                     ->orderBy($wrapper->getOrderColumn(), $wrapper->getOrderDirection())
                     ->limit($wrapper->getLimit())
                     ->offset($wrapper->getOffset());

        return new PaginationResponse($query);
    }
}
