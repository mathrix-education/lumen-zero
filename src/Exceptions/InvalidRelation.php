<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions;

use Illuminate\Database\Eloquent\RelationNotFoundException;
use Mathrix\Lumen\Zero\Exceptions\Http\Http400BadRequest;
use Throwable;

/**
 * Thrown when querying an invalid relation on a model
 */
class InvalidRelation extends Http400BadRequest
{
    public function __construct(RelationNotFoundException $exception, ?Throwable $previous = null)
    {
        parent::__construct($data, $message, $previous);
    }
}
