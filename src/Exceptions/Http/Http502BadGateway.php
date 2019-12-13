<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions\Http;

/**
 * Send a HTTP/502 Bad Gateway
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/502
 *
 * @codeCoverageIgnore
 */
abstract class Http502BadGateway extends Http
{
    /** THE HTTP error standard code */
    protected const CODE = 502;
}
