<?php

namespace Mathrix\Lumen\Tests\Standard;

use Mathrix\Lumen\Tests\REST\RESTTrait;

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
