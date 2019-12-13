<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions\Http;

/**
 * Send a HTTP/451 Unavailable For Legal Reasons
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/451
 * @codeCoverageIgnore
 */
abstract class Http451UnavailableForLegalReasons extends Http
{
    /** THE HTTP error standard code */
    protected const CODE = 451;
}
