<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Models\Traits;

use function with;

trait IsSearchable
{
    protected $searchable = [];

    /**
     * @return string[] The searchable columns of the model.
     */
    public static function getSearchableColumns(): array
    {
        return with(new static())->searchable;
    }
}
