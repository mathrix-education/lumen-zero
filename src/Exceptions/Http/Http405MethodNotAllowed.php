<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions\Http;

/**
 * Send a HTTP/405 Method Not Allowed
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/405
 *
 * @codeCoverageIgnore
 */
abstract class Http405MethodNotAllowed extends Http
{
    /** THE HTTP error standard code */
    protected const CODE = 405;
}
