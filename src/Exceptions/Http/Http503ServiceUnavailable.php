<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions\Http;

/**
 * Send a HTTP/503 Service Unavailable
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/503
 *
 * @codeCoverageIgnore
 */
abstract class Http503ServiceUnavailable extends Http
{
    /** THE HTTP error standard code */
    protected const CODE = 503;
}
