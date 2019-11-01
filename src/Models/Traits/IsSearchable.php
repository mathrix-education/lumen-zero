<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Models\Traits;

use function with;

trait IsSearchable
{
    /** @var string[]|null The searchable columns of the model. */
    protected $searchable = null;

    /**
     * @return string[] The searchable columns of the model. By default, it will use the "fillable" property
     * if "searchable" property is null.
     */
    public static function getSearchableColumns(): array
    {
        $instance = new static();

        return $instance->getSearchable() === null ? $instance->getFillable() : $instance->getSearchable();
    }

    /**
     * @return string[]|null
     */
    public function getSearchable(): ?array
    {
        return $this->searchable;
    }
}
