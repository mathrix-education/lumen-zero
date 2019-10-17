<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Testing\Traits;

use Closure;

trait HasEventHandlers
{
    /** @var array The event handlers. */
    protected $handlers = [];

    /**
     * Register an event handler.
     *
     * @param string  $name     The event name.
     * @param Closure $callback
     */
    public function handler(string $name, Closure $callback)
    {
        if (empty($this->handlers[$name])) {
            $this->handlers[$name] = [];
        }

        $this->handlers[$name][] = $callback;
    }

    /**
     * Fire the a named event.
     *
     * @param string $name       The event name.
     * @param mixed  ...$payload
     */
    public function event(string $name, ...$payload)
    {
        if (empty($this->handlers[$name])) {
            return;
        }

        foreach ($this->handlers[$name] as $callback) {
            $callback(...$payload);
        }
    }
}
