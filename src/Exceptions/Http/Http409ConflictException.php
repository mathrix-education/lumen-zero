<?php

namespace Mathrix\Lumen\Exceptions\Http;

/**
 * Class Http409ConflictException.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
class Http409ConflictException extends HttpException
{
    /** The HTTP error standard name */
    protected const ERROR = "Conflict";
    /** THE HTTP error standard code */
    protected const CODE = 409;
}
