<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Mathrix\Lumen\Zero\Exceptions\Validation;

/**
 * @mixin BaseModel
 */
trait Orderable
{
    protected $orderColumn       = 'order';
    protected $orderGroupColumns = [];

    public static function bootOrderable()
    {
        static::saving(static function (self $model) {
            $model->orderSaving();
        });
        static::deleted(static function (self $model) {
            $model->orderDeleted();
        });
    }

    public function getOrder(): ?int
    {
        return $this->getAttribute($this->orderColumn);
    }

    public function setOrder(int $order): void
    {
        $this->setAttribute($this->orderColumn, $order);
    }

    /**
     * Get the query based on the model and the foreign to group.
     *
     * @return Builder
     */
    private function getOrderQuery(): Builder
    {
        $query = $this->newQuery();

        foreach ($this->orderGroupColumns as $column) {
            $query->where($column, '=', $this->getAttribute($column));
        }

        return $query;
    }

    /**
     * Allow model reordering by moving next models depending on initial and final order request.
     *
     * @throws Validation
     */
    public function orderSaving(): void
    {
        $modelCount = $this->getOrderQuery()->count();

        if ($this->getOrder() === null) {
            $this->setOrder($modelCount);

            return;
        }

        if (!$this->isDirty($this->orderColumn)) {
            return;
        }

        // Check that the new order is not out of range
        $validator = Validator::make(
            [$this->orderColumn => $this->getOrder()],
            [$this->orderColumn => "numeric|between:0,$modelCount"]
        );

        if ($validator->fails()) {
            throw new Validation($validator->errors()->getMessages());
        }

        $oldOrder = $this->getOriginal($this->orderColumn) ?? $modelCount;

        if ($oldOrder < $this->getOrder()) {
            $min      = $oldOrder + 1;
            $max      = $this->getOrder();
            $operator = '-';
        } else {
            $min      = $this->getOrder();
            $max      = $oldOrder - 1;
            $operator = '+';
        }

        $this->getOrderQuery()
            ->whereBetween($this->orderColumn, [$min, $max])
            ->update(['order' => DB::raw("`order` $operator 1")]);
    }

    /**
     * Handle model re-ordering deletion by reducing orders of next models.
     */
    public function orderDeleted(): void
    {
        $this->getOrderQuery()
            ->where($this->orderColumn, '>', $this->getOrder())
            ->update(['order' => DB::raw('`order` - 1')]);
    }
}
