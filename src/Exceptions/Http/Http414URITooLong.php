<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions\Http;

/**
 * Send a HTTP/414 URI Too Long
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/414
 * @codeCoverageIgnore
 */
abstract class Http414URITooLong extends Http
{
    /** THE HTTP error standard code */
    protected const CODE = 414;
}
