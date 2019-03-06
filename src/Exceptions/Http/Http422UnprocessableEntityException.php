<?php

namespace Mathrix\Lumen\Exceptions\Http;

/**
 * Class Http422UnprocessableEntityException.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
class Http422UnprocessableEntityException extends HttpException
{
    /** The HTTP error standard name */
    protected const ERROR = "Unprocessable Entity";
    /** THE HTTP error standard code */
    protected const CODE = 422;
}
