<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Models\Traits;

trait IsSearchable
{
    /** @var string[]|null The searchable columns of the model. */
    protected $searchable = null;

    /**
     * @return string[] The searchable columns of the model. By default, it will use the "fillable" property
     * if "searchable" property is null.
     */
    public function getSearchableColumns(): array
    {
        return $this->getSearchable() ?? $this->getFillable();
    }

    /**
     * @return string[]|null
     */
    public function getSearchable(): ?array
    {
        return $this->searchable;
    }
}
