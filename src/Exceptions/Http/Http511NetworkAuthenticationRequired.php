<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions\Http;

/**
 * Send a HTTP/511 Network Authentication Required
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/511
 *
 * @codeCoverageIgnore
 */
abstract class Http511NetworkAuthenticationRequired extends Http
{
    /** THE HTTP error standard code */
    protected const CODE = 511;
}
