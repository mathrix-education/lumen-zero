<?php

namespace Mathrix\Lumen\Tests\Traits;

/**
 * Trait DebugTrait.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 *
 * @mixin RESTTrait
 */
trait DebugTrait
{
    /**
     * Handle response printing.
     *
     * @param \Exception|\Throwable $e
     *
     * @throws \Throwable
     */
    protected function onNotSuccessfulTest(\Throwable $e)
    {
        $this->debug();
        parent::onNotSuccessfulTest($e);
    }


    /**
     * Debug the response.
     *
     * @throws \Exception
     */
    public function debug()
    {
        echo $this->currentUri . "\n";
        echo json_encode($this->getJsonResponseData(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
