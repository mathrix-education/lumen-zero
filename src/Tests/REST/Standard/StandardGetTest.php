<?php

namespace Mathrix\Lumen\Zero\Tests\REST\Standard;

use Mathrix\Lumen\Zero\Tests\REST\RESTTrait;

/**
 * Class StandardGetTest.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 *
 * @mixin RESTTrait
 */
trait StandardGetTest
{
    /**
     * GET /{base}/{modelId}
     * Standard get test.
     */
    public function testGet()
    {
        $this->assertRestGetSuccess();
    }
}
