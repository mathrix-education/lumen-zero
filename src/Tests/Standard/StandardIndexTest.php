<?php

namespace Mathrix\Lumen\Tests\Standard;

use Mathrix\Lumen\Tests\Traits\RESTTrait;

/**
 * Trait StandardIndexTest.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 *
 * @mixin RESTTrait
 */
trait StandardIndexTest
{
    /**
     * GET /{base}/{page}/{perPage}
     * Standard index test.
     */
    public function testIndex()
    {
        $this->assertRestIndexSuccess();
    }
}
