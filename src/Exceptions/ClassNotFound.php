<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions;

use Mathrix\Lumen\Zero\Exceptions\Http\Http500InternalServerError;
use Throwable;

class ClassNotFound extends Http500InternalServerError
{
    public function __construct(string $class, ?Throwable $previous = null)
    {
        $data    = ['class' => $class];
        $message = "Could not find class $class";
        parent::__construct($data, $message, $previous);
    }
}
