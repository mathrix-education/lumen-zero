<?php

namespace Mathrix\Lumen\Middleware;

use Closure;
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Http\Request;
use Mathrix\Lumen\Exceptions\Http\Http401UnauthorizedException;

/**
 * Class LoggedMiddleware.
 * Forbid action to users who are noy authenticated.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
class LoggedMiddleware
{
    /**
     * @var Auth The authentication guard factory instance.
     */
    protected $auth;


    /**
     * Create a new middleware instance.
     *
     * @param Auth|AuthManager $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }


    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string|null $guard
     * @return mixed
     *
     * @throws Http401UnauthorizedException
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {
        if ($this->auth->guard($guard)->guest()) {
            throw new Http401UnauthorizedException();
        }

        return $next($request);
    }
}
