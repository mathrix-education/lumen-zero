<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions\Http;

/**
 * Send a HTTP/506 Variant Also Negociates
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/506
 *
 * @codeCoverageIgnore
 */
abstract class Http506VariantAlsoNegociates extends Http
{
    /** THE HTTP error standard code */
    protected const CODE = 506;
}
