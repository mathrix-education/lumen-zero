<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions\Http;

/**
 * @codeCoverageIgnore
 */
class Http401Unauthorized extends Http
{
    /** THE HTTP error standard code */
    protected const CODE = 401;
}
