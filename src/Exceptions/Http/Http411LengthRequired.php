<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions\Http;

/**
 * Send a HTTP/411 Length Required
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/411
 * @codeCoverageIgnore
 */
abstract class Http411LengthRequired extends Http
{
    /** THE HTTP error standard code */
    protected const CODE = 411;
}
