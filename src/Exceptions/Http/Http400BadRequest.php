<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions\Http;

/**
 * Send a HTTP/400 Bad Request
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/400
 * @codeCoverageIgnore
 */
abstract class Http400BadRequest extends Http
{
    /** THE HTTP error standard code */
    protected const CODE = 400;
}
