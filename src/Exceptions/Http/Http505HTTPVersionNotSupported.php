<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions\Http;

/**
 * Send a HTTP/505 HTTP Version Not Supported
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/505
 *
 * @codeCoverageIgnore
 */
abstract class Http505HTTPVersionNotSupported extends Http
{
    /** THE HTTP error standard code */
    protected const CODE = 505;
}
