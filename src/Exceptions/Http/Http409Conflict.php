<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions\Http;

/**
 * Send a HTTP/409 Conflict
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/409
 * @codeCoverageIgnore
 */
abstract class Http409Conflict extends Http
{
    /** THE HTTP error standard code */
    protected const CODE = 409;
}
