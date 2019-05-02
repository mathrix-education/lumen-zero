<?php

namespace Mathrix\Lumen\unit\Exceptions\Http;

use Mathrix\Lumen\Exceptions\Http\HttpException;
use PHPUnit\Framework\TestCase;

/**
 * Class HttpExceptionTest.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.1.0
 */
class HttpExceptionTest extends TestCase
{
    public function getHttpException()
    {
        return new class() extends HttpException
        {
        };
    }


    public function testGetError()
    {
        $vectors = [
            "Http429TooManyRequestsException" => "TooManyRequests",
            "Http501NotImplementedException" => "NotImplemented",
            "ProductAlreadyBoughtException" => "ProductAlreadyBought"
        ];

        foreach ($vectors as $test => $expected) {
            $this->assertEquals($expected, $this->getHttpException()->getError($test));
        }
    }
}
