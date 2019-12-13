<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions\Http;

/**
 * Send a HTTP/504 Gateway Timeout
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/504
 *
 * @codeCoverageIgnore
 */
abstract class Http504GatewayTimeout extends Http
{
    /** THE HTTP error standard code */
    protected const CODE = 504;
}
