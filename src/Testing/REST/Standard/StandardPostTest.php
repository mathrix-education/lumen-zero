<?php

namespace Mathrix\Lumen\Zero\Testing\REST\Standard;

use Mathrix\Lumen\Zero\Testing\REST\RESTTrait;

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
