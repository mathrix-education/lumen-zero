<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions;

use Mathrix\Lumen\Zero\Exceptions\Http\Http401Unauthorized;
use Throwable;
use function get_class;
use function is_string;
use function trans;

/**
 * Thrown when a user is denied by a policy ability.
 */
class PolicyDenied extends Http401Unauthorized
{
    public function __construct($policy, string $ability, ?Throwable $previous = null)
    {
        $policy = is_string($policy) ? $policy : get_class($policy);
        $data   = ['policy' => $policy, 'ability' => $ability];

        parent::__construct($data, trans('zero.exceptions.policy_denied', $data), $previous);
    }
}
