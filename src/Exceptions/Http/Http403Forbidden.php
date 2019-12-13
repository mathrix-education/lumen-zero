<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions\Http;

/**
 * Send a HTTP/403 Forbidden
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/403
 * @codeCoverageIgnore
 */
abstract class Http403Forbidden extends Http
{
    /** THE HTTP error standard code */
    protected const CODE = 403;
}
