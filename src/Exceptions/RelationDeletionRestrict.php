<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions;

use Mathrix\Lumen\Zero\Exceptions\Http\Http409Conflict;
use Mathrix\Lumen\Zero\Models\BaseModel;
use Throwable;
use function trans;

/**
 * Thrown when a model is being deleted but has restricting relations.
 */
class RelationDeletionRestrict extends Http409Conflict
{
    public function __construct(BaseModel $model, string $relation, ?Throwable $previous = null)
    {
        parent::__construct(
            [$model->getKeyName() => $model->getKey()],
            trans('relation_deletion_restrict', ['key' => $model->getKey(), 'relation' => $relation]),
            $previous
        );
    }
}
