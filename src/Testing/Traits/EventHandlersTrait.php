<?php

namespace Mathrix\Lumen\Zero\Testing\Traits;

use Closure;

/**
 * Class EventHandlersTrait.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 2.0.0
 */
trait EventHandlersTrait
{
    /** @var array The event handlers. */
    protected $handlers = [];


    /**
     * Register an event handler.
     *
     * @param $name
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
     * @param string $name
     * @param mixed ...$payload
     */
    public function event(string $name, ...$payload)
    {
        if (!empty($this->handlers[$name])) {
            foreach ($this->handlers[$name] as $callback) {
                $callback(...$payload);
            }
        }
    }
}
