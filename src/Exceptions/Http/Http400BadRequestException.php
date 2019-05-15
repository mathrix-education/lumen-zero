<?php

namespace Mathrix\Lumen\Zero\Exceptions\Http;

/**
 * Class Http400BadRequestException.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
class Http400BadRequestException extends HttpException
{
    /** THE HTTP error standard code */
    protected const CODE = 400;
}
