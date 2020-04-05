<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Responses;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class PaginationResponse extends DataResponse
{
    /**
     * @param Builder|Relation $query    The Eloquent query with pagination parameters injected.
     * @param Closure|null     $callback A custom callback to run the model collection.
     * @param int              $status   The HTTP status code (default 200).
     * @param array            $headers  The custom HTTP headers.
     * @param int              $options  The json_encode function options.
     */
    public function __construct($query, ?Closure $callback = null, $status = 200, $headers = [], $options = 0)
    {
        $totalQuery         = (clone $query)->getQuery();
        $totalQuery->limit  = null;
        $totalQuery->offset = null;

        $models = $query->get();

        if ($callback !== null) {
            $models = $callback($models);
        }

        if ($query instanceof Relation) {
            $query = $query->getQuery();
        }

        $meta = [
            'page'     => (int)($query->getQuery()->offset / $query->getQuery()->limit),
            'per_page' => $query->getQuery()->limit,
            'total'    => $totalQuery->count(),
        ];

        parent::__construct($models, $meta, $status, $headers, $options);
    }
}
