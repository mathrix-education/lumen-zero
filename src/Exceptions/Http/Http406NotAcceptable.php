<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions\Http;

/**
 * Send a HTTP/406 Not Acceptable
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/406
 *
 * @codeCoverageIgnore
 */
abstract class Http406NotAcceptable extends Http
{
    /** THE HTTP error standard code */
    protected const CODE = 406;
}
