<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions\Http;

/**
 * Send a HTTP/410 Gone
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/410
 *
 * @codeCoverageIgnore
 */
abstract class Http410Gone extends Http
{
    /** THE HTTP error standard code */
    protected const CODE = 410;
}
