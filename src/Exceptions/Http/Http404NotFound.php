<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions\Http;

/**
 * Send a HTTP/404 Not Found
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/404
 * @codeCoverageIgnore
 */
abstract class Http404NotFound extends Http
{
    /** THE HTTP error standard code */
    protected const CODE = 404;
}
