<?php

namespace Mathrix\Lumen\Checks;

use Illuminate\Support\Str;
use JsonSerializable;

/**
 * Class BaseCheck.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
abstract class BaseCheck implements JsonSerializable
{
    /** @var float $start The starting time of the check. */
    protected $start;
    /** @var float $end The starting time of the check. */
    protected $end;
    /** @var float $latency The duration in seconds of the check. */
    protected $latency;
    /** @var string $status The status of the check. */
    protected $status;
    /** @var string[] $errors The errors. */
    protected $errors;


    /**
     * Execute the check.
     */
    public function execute()
    {
        $this->start = microtime(true);
        $this->status = $this->run();
        $this->end = microtime(true);
        $this->latency = $this->end - $this->start;

        return $this;
    }


    /**
     * Execute the check and return the current status.
     * @return string
     */
    abstract protected function run(): string;


    /**
     * Get the JSON representation of the check.
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        $data = [
            "name" => Str::snake(class_basename($this)),
            "status" => $this->status,
            "start" => $this->start,
            "end" => $this->end,
            "latency" => $this->latency,
            "latency_ms" => round($this->latency * 1000, 2)
        ];

        if (!empty($this->errors)) {
            $data["errors"] = $this->errors;
        }

        return $data;
    }
}
