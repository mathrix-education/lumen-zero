<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions;

use Mathrix\Lumen\Zero\Exceptions\Http\Http422UnprocessableEntity;

class Validation extends Http422UnprocessableEntity
{
    protected $message = 'Submitted data failed to pass validation.';
}
