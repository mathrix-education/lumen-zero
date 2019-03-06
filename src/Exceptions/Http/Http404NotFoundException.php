<?php

namespace Mathrix\Lumen\Exceptions\Http;

/**
 * Class Http404NotFoundException.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
class Http404NotFoundException extends HttpException
{
    /** The HTTP error standard name */
    protected const ERROR = "Not Found";
    /** THE HTTP error standard code */
    protected const CODE = 404;
    /** @var string Exception message; has to be manually defined */
    protected $message = "Not Found";
}
