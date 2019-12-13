<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions\Http;

/**
 * Send a HTTP/422 Unprocessable Entity
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/422
 *
 * @codeCoverageIgnore
 */
abstract class Http422UnprocessableEntity extends Http
{
    /** THE HTTP error standard code */
    protected const CODE = 422;
}
