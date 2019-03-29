<?php

namespace Mathrix\Lumen\Tests\Standard;

use Mathrix\Lumen\Tests\Traits\RESTTrait;

/**
 * Class StandardPostTest.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 *
 * @mixin RESTTrait
 */
trait StandardPostTest
{
    /**
     * POST /{base}
     * Standard post test.
     */
    public function testPost()
    {
        $this->assertRestPostSuccess();
    }
}
