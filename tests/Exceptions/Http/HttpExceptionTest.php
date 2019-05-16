<?php

namespace Mathrix\Lumen\Exceptions\Http;

use Mathrix\Lumen\Zero\Exceptions\Http\HttpException;
use Mathrix\Lumen\Zero\Testing\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

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
        return DataProvider::makeDataProvider([
            "TooManyRequests" => "Http429TooManyRequestsException",
            "NotImplemented" => "Http501NotImplementedException",
            "ProductAlreadyBought" => "ProductAlreadyBoughtException"
        ]);
    }


    /**
     * @param string $expected
     * @param string $vector
     *
     * @throws ReflectionException
     *
     * @dataProvider getErrorDataProvider
     * @covers       \Mathrix\Lumen\Exceptions\Http\HttpException
     */
    public function testGetError(string $expected, string $vector)
    {
        /** @var MockObject|HttpException $subject */
        $subject = $this->getMockForAbstractClass(HttpException::class);
        $this->assertEquals($expected, $subject->getError($vector));
    }
}
