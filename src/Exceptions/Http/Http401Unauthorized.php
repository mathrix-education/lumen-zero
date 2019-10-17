<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions\Http;

class Http401Unauthorized extends Http
{
    /** THE HTTP error standard code */
    protected const CODE = 401;
}
