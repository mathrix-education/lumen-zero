<?php

namespace Mathrix\Lumen\Tests\Standard;

use Mathrix\Lumen\Tests\Traits\RESTTrait;

/**
 * Class StandardDeleteTest.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 *
 * @mixin RESTTrait
 */
trait StandardDeleteTest
{
    /**
     * DELETE /{base}/{modelId}
     * Standard delete test.
     */
    public function testDelete()
    {
        $this->assertRestDeleteSuccess();
    }
}
