<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions\Http;

/**
 * Send a HTTP/408 Request Timeout
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/408
 *
 * @codeCoverageIgnore
 */
abstract class Http408RequestTimeout extends Http
{
    /** THE HTTP error standard code */
    protected const CODE = 408;
}
