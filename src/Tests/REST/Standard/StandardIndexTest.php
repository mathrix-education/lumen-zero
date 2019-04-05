<?php

namespace Mathrix\Lumen\Tests\REST\Standard;

use Mathrix\Lumen\Tests\REST\RESTTrait;

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
