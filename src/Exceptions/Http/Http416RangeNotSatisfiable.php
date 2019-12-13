<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions\Http;

/**
 * Send a HTTP/416 Range Not Satisfiable
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/416
 * @codeCoverageIgnore
 */
abstract class Http416RangeNotSatisfiable extends Http
{
    /** THE HTTP error standard code */
    protected const CODE = 416;
}
