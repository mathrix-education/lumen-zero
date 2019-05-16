<?php

namespace Mathrix\Lumen\Exceptions\Http;

use Mathrix\Lumen\Zero\Exceptions\Http\HttpException;
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
    public function getErrorDataProvider()
    {
        return [
            ["TooManyRequests", "Http429TooManyRequestsException"],
            ["NotImplemented", "Http501NotImplementedException"],
            ["ProductAlreadyBought", "ProductAlreadyBoughtException"]
        ];
    }


    /**
     * @dataProvider getErrorDataProvider
     *
     * @param string $expected
     * @param string $vector
     */
    public function testGetError(string $expected, string $vector)
    {
        $this->assertEquals($expected, $this->getHttpException()->getError($vector));
    }


    public function getHttpException()
    {
        return new class() extends HttpException
        {
        };
    }
}
