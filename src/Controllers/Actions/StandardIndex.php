<?php

namespace Mathrix\Lumen\Zero\Controllers\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Mathrix\Lumen\Zero\Exceptions\Http\Http400BadRequestException;
use Mathrix\Lumen\Zero\Responses\PaginationJsonResponse;

/**
 * Trait StandardIndex.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 2.0.0
 */
trait StandardIndex
{
    /**
     * GET /models
     *
     * @param Request $request The Illuminate HTTP request.
     *
     * @return PaginationJsonResponse
     * @throws Http400BadRequestException
     */
    public function standardIndex(Request $request): PaginationJsonResponse
    {
        $ability = $this->getAbility("index", "standard");
        $this->canOrFail($request, $ability, $this->modelClass);

        // Make the Eloquent query
        $query = $this->modelClass::query()
            ->with($this->with["std:index"] ?? []);

        return new PaginationJsonResponse($query);
    }
}
