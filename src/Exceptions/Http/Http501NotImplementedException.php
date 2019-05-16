<?php

namespace Mathrix\Lumen\Zero\Exceptions\Http;

/**
 * Class Http501NotImplementedException.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
class Http501NotImplementedException extends HttpException
{
    /** THE HTTP error standard code */
    protected const CODE = 501;
    /** The HTTP error standard name */
    protected $message = "This feature has not yet been implemented.";
}
