<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions\Http;

/**
 * Send a HTTP/401 Unauthorized
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/401
 * @codeCoverageIgnore
 */
abstract class Http401Unauthorized extends Http
{
    /** THE HTTP error standard code */
    protected const CODE = 401;
}
