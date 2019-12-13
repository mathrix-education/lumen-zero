<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions\Http;

/**
 * Send a HTTP/431 Request Header Fields Too Large
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/431
 * @codeCoverageIgnore
 */
abstract class Http431RequestHeaderFieldsTooLarge extends Http
{
    /** THE HTTP error standard code */
    protected const CODE = 431;
}
