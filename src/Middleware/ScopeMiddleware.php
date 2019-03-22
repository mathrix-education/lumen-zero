<?php

namespace Mathrix\Lumen\Middleware;

use Closure;
use Illuminate\Http\Request;
use Mathrix\Lumen\Exceptions\Http\Http401UnauthorizedException;

/**
 * Class ScopeMiddleware.
 * Forbid action to users who does not have ONE OF the given scopes.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
class ScopeMiddleware
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string[] ...$scopes The allowed scopes
     * @return mixed
     *
     * @throws Http401UnauthorizedException
     */
    public function handle(Request $request, Closure $next, ...$scopes)
    {
        if (!$request->user() || !$request->user()->token()) {
            throw new Http401UnauthorizedException(null, "Not authenticated.");
        }

        foreach ($scopes as $scope) {
            if ($request->user()->tokenCan($scope)) {
                return $next($request);
            }
        }

        throw new Http401UnauthorizedException(["scopes" => $scopes], "One of the given scopes is required.");
    }
}
