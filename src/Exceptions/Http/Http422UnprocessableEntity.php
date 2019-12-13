<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions\Http;

/**
 * @codeCoverageIgnore
 */
abstract class Http422UnprocessableEntity extends Http
{
    /** THE HTTP error standard code */
    protected const CODE = 422;
}
