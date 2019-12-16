<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions;

use Mathrix\Lumen\Zero\Exceptions\Http\Http404NotFound;
use Throwable;
use function trans;

/**
 * Thrown when a route does not exist.
 */
class RouteNotFound extends Http404NotFound
{
    public function __construct(string $route, ?Throwable $previous = null)
    {
        $data = ['route' => $route];
        parent::__construct($data, trans('zero.exceptions.route_not_found', $data), $previous);
    }
}
