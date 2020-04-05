<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions;

use Mathrix\Lumen\Zero\Exceptions\Http\Http404NotFound;

class ModelNotFound extends Http404NotFound
{
    protected $message = 'Unable to find a model matching those criteria.';
}
