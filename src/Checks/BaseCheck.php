<?php

namespace Mathrix\Lumen\Checks;

/**
 * Class BaseCheck.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
abstract class BaseCheck
{
    /** @var float $start The starting time of the check. */
    protected $start;
    /** @var float $end The starting time of the check. */
    protected $end;
    /** @var float $latency The duration in seconds of the check. */
    protected $latency;
    /** @var string $status The status of the check. */
    protected $status;


    /**
     * Execute the check.
     */
    public function execute()
    {
        $this->start = microtime(true);
        try {
            $this->status = $this->run();
        } catch (\Exception $e) {
            // Kill exception
        }
        $this->end = microtime(true);
        $this->latency = $this->end - $this->start;
    }


    /**
     * Execute the check and return the current status.
     * @return string
     */
    abstract protected function run(): string;
}
