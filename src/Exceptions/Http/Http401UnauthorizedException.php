<?php

namespace Mathrix\Lumen\Exceptions\Http;

/**
 * Class Http401UnauthorizedException.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
class Http401UnauthorizedException extends HttpException
{
    /** THE HTTP error standard code */
    protected const CODE = 401;
}
