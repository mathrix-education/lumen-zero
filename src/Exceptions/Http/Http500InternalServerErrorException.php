<?php

namespace Mathrix\Lumen\Exceptions\Http;

/**
 * Class Http500InternalServerErrorException.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
class Http500InternalServerErrorException extends HttpException
{
    /** THE HTTP error standard code */
    protected const CODE = 500;
    /** @var string Exception message; has to be manually defined */
    protected $message = "Internal server exception. This is probably not your fault; please report this problem to " .
    "the back-end team.";
}
