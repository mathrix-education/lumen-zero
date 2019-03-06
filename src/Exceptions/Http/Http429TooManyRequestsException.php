<?php
namespace Mathrix\Lumen\Exceptions\Http;

/**
 * Class Http429TooManyRequests.
 *
 * @author    Mathieu Bour <mathieu.tin.bour@gmail.com>
 * @author    Jérémie Levain <munezero999@live.fr>
 * @since     1.0.0
 * @copyright Mathrix Education SA
 * @package   App\Exceptions
 */
class Http429TooManyRequestsException extends HttpException
{
    /** The HTTP error standard name */
    protected const ERROR = "Too Many Requests";
    /** THE HTTP error standard code */
    protected const CODE = null;
}
