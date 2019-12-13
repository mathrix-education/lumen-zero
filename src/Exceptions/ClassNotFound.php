<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions;

use Mathrix\Lumen\Zero\Exceptions\Http\Http500InternalServerError;
use Throwable;
use function trans;

/**
 * Thrown when a class cannot be found.
 */
class ClassNotFound extends Http500InternalServerError
{
    public function __construct(string $class, ?Throwable $previous = null)
    {
        parent::__construct(
            ['class' => $class],
            trans('zero.exceptions.class_not_found', ['class' => $class]),
            $previous
        );
    }
}
