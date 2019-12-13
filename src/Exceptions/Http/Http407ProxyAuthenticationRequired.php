<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions\Http;

/**
 * Send a HTTP/407 Proxy Authentication Required
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/407
 *
 * @codeCoverageIgnore
 */
abstract class Http407ProxyAuthenticationRequired extends Http
{
    /** THE HTTP error standard code */
    protected const CODE = 407;
}
