<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions\Http;

/**
 * Send a HTTP/500 Internal Server Error
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/500
 *
 * @codeCoverageIgnore
 */
abstract class Http500InternalServerError extends Http
{
    /** THE HTTP error standard code */
    protected const CODE = 500;
}
