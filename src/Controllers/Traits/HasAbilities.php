<?php


namespace Mathrix\Lumen\Zero\Controllers\Traits;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Mathrix\Lumen\Zero\Exceptions\Http\Http401UnauthorizedException;

/**
 * Trait HasAbilities.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 2.0.0
 */
trait HasAbilities
{
    /**
     * Get the right ability.
     *
     * @param string $method The HTTP method.
     * @param string $type The request type (standard|relation).
     * @param string $key The key.
     * @param string|null $relation The relation, if necessary.
     *
     * @return string
     */
    protected function getAbility(string $method, string $type, string $key = "id", string $relation = null)
    {
        if ($type === "standard") {
            if ($key !== "id") {
                return "{$method}By" . ucfirst(Str::camel($key));
            }

            return $method;
        } elseif ($type === "relation") {
            $relation = ucfirst(Str::camel($relation));

            if ($key !== "id") {
                return "{$method}{$relation}By" . ucfirst(Str::camel($key));
            }

            return "{$method}{$relation}";
        } else {
            return null;
        }
    }


    /**
     * Check if the Gate denies the request.
     *
     * @param Request $request
     * @param string $ability
     * @param null $model
     *
     * @throws Http401UnauthorizedException
     */
    protected function canOrFail(Request $request, string $ability, $model = null)
    {
        if ($this->shouldUsePolicy($ability) && Gate::forUser($request->user())->denies($ability, $model)) {
            throw new Http401UnauthorizedException([
                "model_class" => $this->modelClass,
                "ability" => $ability
            ], "Failed to pass $ability policy");
        }
    }


    /**
     * Check if the controller should use a policy for a given ability.
     *
     * @param string $ability The policy ability.
     *
     * @return bool
     */
    protected function shouldUsePolicy(string $ability): bool
    {
        $policyClass = Gate::getPolicyFor($this->modelClass);

        return $policyClass !== null && method_exists($policyClass, $ability);
    }
}
