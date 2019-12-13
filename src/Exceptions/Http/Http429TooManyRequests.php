<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions\Http;

/**
 * Send a HTTP/429 Too Many Requests
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/429
 *
 * @codeCoverageIgnore
 */
abstract class Http429TooManyRequests extends Http
{
    /** THE HTTP error standard code */
    protected const CODE = 429;
}
