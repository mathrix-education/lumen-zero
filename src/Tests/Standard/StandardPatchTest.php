<?php

namespace Mathrix\Lumen\Tests\Standard;

use Mathrix\Lumen\Tests\Traits\RESTTrait;

/**
 * Trait StandardPatchTest.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 *
 * @mixin RESTTrait
 */
trait StandardPatchTest
{
    /**
     * PATCH /{base}/{modelId}
     * Standard patch test.
     */
    public function testPatch()
    {
        $this->assertRestPatchSuccess();
    }
}
