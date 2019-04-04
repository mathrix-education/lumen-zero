<?php

namespace Mathrix\Lumen\Exceptions;

use Mathrix\Lumen\Exceptions\Http\Http422UnprocessableEntityException;

/**
 * Class ValidationException.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
class ValidationException extends Http422UnprocessableEntityException
{
    protected const ERROR = "validation";
    protected $message = "Submitted data failed to pass validation.";
}
