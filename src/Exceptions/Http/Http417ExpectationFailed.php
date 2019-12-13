<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions\Http;

/**
 * Send a HTTP/417 Expectation Failed
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/417
 *
 * @codeCoverageIgnore
 */
abstract class Http417ExpectationFailed extends Http
{
    /** THE HTTP error standard code */
    protected const CODE = 417;
}
