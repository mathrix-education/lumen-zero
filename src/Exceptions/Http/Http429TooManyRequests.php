<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions\Http;

class Http429TooManyRequests extends Http
{
    /** THE HTTP error standard code */
    protected const CODE = 429;
}
