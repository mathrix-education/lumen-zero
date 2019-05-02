<?php

namespace Mathrix\Lumen\exceptions\standard;

use Mathrix\Lumen\Exceptions\Http\HttpException;

/**
 * Class Http405MethodNotAllowed.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
class Http405MethodNotAllowed extends HttpException
{
    /** THE HTTP error standard code */
    protected const CODE = 405;
}
