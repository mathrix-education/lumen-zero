<?php

namespace Mathrix\Lumen\Exceptions\Http;

/**
 * Class Http501NotImplementedException.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
class Http501NotImplementedException extends HttpException
{
    /** The HTTP error standard name */
    protected const ERROR = "Not Implemented";
    /** THE HTTP error standard code */
    protected const CODE = 501;
}
