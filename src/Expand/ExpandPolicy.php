<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Expand;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;
use Mathrix\Lumen\Zero\Controllers\Traits\HasAbilities;
use Mathrix\Lumen\Zero\Exceptions\PolicyDenied;
use Mathrix\Lumen\Zero\Models\BaseModel;
use function array_shift;
use function explode;
use function get_class;
use function implode;
use function in_array;
use function is_string;
use function with;

class ExpandPolicy
{
    /**
     * Check recursively the given user abilities regarding the expand key..
     *
     * @param HasAbilities|null $user       The user.
     * @param string            $modelClass The model class.
     * @param string            $expand     The expand key.
     *
     * @throws PolicyDenied
     */
    public function can($user, string $modelClass, string $expand): void
    {
        // Retrieve the keys
        [$relationKey, $childKey] = $this->parseRelationKey($expand);

        /** @var BaseModel $model A model instance */
        $model = with(new $modelClass());
        /** @var Relation $relation The involved relation */
        $relation = $model->{$relationKey}();

        $ability      = null;
        $related      = null;
        $relatedModel = get_class($relation->getModel());

        if ($this->isSingleRelation($relation)) {
            $ability = 'read';
            $related = $model->{$relationKey};
        } elseif ($this->isMultipleRelation($relation)) {
            $ability = 'list';
            $related = $relatedModel;
        }

        $gate = Gate::forUser($user);

        if ($gate->denies($ability, $related)) {
            $policy = $gate->getPolicyFor($related);
            throw new PolicyDenied($policy, $ability);
        }

        if ($childKey === null) {
            return;
        }

        $this->can($user, $relatedModel, $childKey);
    }

    private function getRelationClass($relation)
    {
        return is_string($relation) ? $relation : get_class($relation);
    }

    private function isSingleRelation($relation): bool
    {
        return in_array(
            $this->getRelationClass($relation),
            [BelongsTo::class, HasOne::class, HasOneThrough::class]
        );
    }

    private function isMultipleRelation($relation): bool
    {
        return in_array(
            $this->getRelationClass($relation),
            [HasMany::class, HasManyThrough::class, BelongsToMany::class]
        );
    }

    private function parseRelationKey(string $expand): array
    {
        $parts       = explode('.', $expand);
        $relationKey = array_shift($parts);
        $childKey    = implode('.', $parts);
        $childKey    = !empty($childKey) ? $childKey : null;

        return [$relationKey, $childKey];
    }
}
