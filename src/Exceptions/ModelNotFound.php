<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions\Models\Models;

use Mathrix\Lumen\Zero\Exceptions\Http\Http404NotFound;

/**
 * @codeCoverageIgnore
 */
class ModelNotFound extends Http404NotFound
{
    protected $message = 'Unable to find a model matching those criteria.';
}
