<?php

namespace Mathrix\Lumen\Exceptions;

use Mathrix\Lumen\Exceptions\Http\Http500InternalServerErrorException;
use Throwable;

/**
 * Class InvalidArgument.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
class InvalidArgument extends Http500InternalServerErrorException
{
    protected const ERROR = "invalid_argument";
}
