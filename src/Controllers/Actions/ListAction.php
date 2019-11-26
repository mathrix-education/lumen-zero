<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Controllers\Actions;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Mathrix\Lumen\Zero\Controllers\QueryExtractor;
use Mathrix\Lumen\Zero\Responses\PaginationResponse;

/**
 * @method Builder query()
 */
trait ListAction
{
    /**
     * GET /{models}
     *
     * @param Request $request The Illuminate HTTP request.
     *
     * @return PaginationResponse
     */
    final public function defaultList(Request $request): PaginationResponse
    {
        $wrapper = new QueryExtractor($request, $this->modelClass);

        $this->canOrFail($request, 'list', $this->modelClass);

        // Make the Eloquent query
        /** @var Builder $query */
        $query = $this->query()
            ->with($wrapper->getWith())
            ->where($wrapper->getWheres())
            ->orderBy($wrapper->getOrderColumn(), $wrapper->getOrderDirection())
            ->limit($wrapper->getLimit())
            ->offset($wrapper->getOffset());

        return new PaginationResponse($query);
    }
}
