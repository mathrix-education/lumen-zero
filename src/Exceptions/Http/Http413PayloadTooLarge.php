<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions\Http;

/**
 * Send a HTTP/413 Payload Too Large
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/413
 *
 * @codeCoverageIgnore
 */
abstract class Http413PayloadTooLarge extends Http
{
    /** THE HTTP error standard code */
    protected const CODE = 413;
}
