<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions\Http;

/**
 * Send a HTTP/501 Not Implemented
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/501
 * @codeCoverageIgnore
 */
abstract class Http501NotImplemented extends Http
{
    /** THE HTTP error standard code */
    protected const CODE = 501;
}
