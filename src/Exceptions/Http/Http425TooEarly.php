<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions\Http;

/**
 * Send a HTTP/425 Too Early
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/425
 * @codeCoverageIgnore
 */
abstract class Http425TooEarly extends Http
{
    /** THE HTTP error standard code */
    protected const CODE = 425;
}
