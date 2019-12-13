<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions\Http;

/**
 * Send a HTTP/415 Unsupported Media Type
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/415
 * @codeCoverageIgnore
 */
abstract class Http415UnsupportedMediaType extends Http
{
    /** THE HTTP error standard code */
    protected const CODE = 415;
}
