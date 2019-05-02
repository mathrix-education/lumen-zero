<?php

namespace Mathrix\Lumen\Exceptions;

use Mathrix\Lumen\Exceptions\Http\Http500InternalServerErrorException;
use Throwable;

/**
 * Class ClassNotFoundException.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
class ClassNotFoundException extends Http500InternalServerErrorException
{
    public function __construct(string $class, ?Throwable $previous = null)
    {
        $data = ["class" => $class];
        $message = "Could not find class $class";
        parent::__construct($data, $message, $previous);
    }
}
