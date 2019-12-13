<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions\Http;

/**
 * Send a HTTP/418 I'm a teapot
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/418
 * @codeCoverageIgnore
 */
abstract class Http418Imateapot extends Http
{
    /** THE HTTP error standard code */
    protected const CODE = 418;
}
