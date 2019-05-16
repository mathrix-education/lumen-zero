<?php

namespace Mathrix\Lumen\Zero\Exceptions\Http;

/**
 * Class Http429TooManyRequestsException.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
class Http429TooManyRequestsException extends HttpException
{
    /** THE HTTP error standard code */
    protected const CODE = 429;
}
