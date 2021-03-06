<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Mathrix\Lumen\Zero\Exceptions\RelationDeletionRestrict;
use function class_basename;
use function count;
use function is_array;
use function lcfirst;
use function ucfirst;
use function with;

/**
 * Base for all models, implement validation on save useless manual disable.
 */
abstract class BaseModel extends Model
{
    use HasValidator;
    use IsSearchable;

    /** @var array $aliases Aliases. */
    protected $aliases = [];

    /**
     * Get the table name.
     *
     * @return string the table name
     */
    public static function getTableName(): string
    {
        return with(new static())->getTable();
    }

    /**
     * Get a random model from the database.
     *
     * @param array $args The where condition. Acceptable format:
     *                    - ("key", "=", "value"),
     *                    - (["key", "=", "value"])
     *                    - ([["key1", "=", "value1"], ["key2", "=", "value2"]])
     *
     * @return static
     */
    public static function random(...$args)
    {
        $query = self::query()->inRandomOrder();

        if (count($args) === 3) {
            $conditions = $args;
        } elseif (!empty($args)) {
            if (!is_array($args[0])) {
                $conditions = [$args];
            } elseif (!empty($args[0])) {
                $conditions = $args;
            }
        }

        if (!empty($conditions)) {
            $query = $query->where($conditions);
        }

        /** @var self $model */
        $model = $query->firstOrFail();

        return $model;
    }

    /**
     * Shortcut for self::query()->where($key, "=", $value)->firstOrFail()
     *
     * @param string $key   The column key
     * @param mixed  $value The column value
     *
     * @return static
     */
    public static function findByOrFail(string $key, $value)
    {
        /** @var self $model */
        $model = self::query()
            ->where($key, '=', $value)
            ->firstOrFail();

        return $model;
    }

    /**
     * Handle aliases.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getAttribute($key)
    {
        if (isset($this->aliases[$key])) {
            return parent::getAttribute($this->aliases[$key]);
        }

        return parent::getAttribute($key);
    }

    /**
     * Handle aliases.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return mixed
     */
    public function setAttribute($key, $value): void
    {
        if (isset($this->aliases[$key])) {
            parent::setAttribute($this->aliases[$key], $value);
        } else {
            parent::setAttribute($key, $value);
        }
    }

    /**
     * Get the selector name based on a given.
     *
     * @param string|null $key The selector key. If not provided, we will use the model primary key instead.
     *
     * @return string
     */
    public function getSelector(?string $key = null): string
    {
        $key  = $key ?? $this->getKeyName();
        $name = class_basename($this);

        return lcfirst($name) . ucfirst($key);
    }

    /**
     * Set default date format to be in compliance with the OpenAPI date-time format.
     *
     * @link https://swagger.io/docs/specification/data-models/data-types/#string
     * @link https://tools.ietf.org/html/rfc3339#section-5.6
     *
     * @param DateTimeInterface $date The DateTimeInterface.
     *
     * @return string The serialized date.
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return Carbon::instance($date)->format('c');
    }

    /**
     * Throws a RelationDeletionRestrict exception if the model has at least one occurrence of the given relation.
     * Useful to assert that a model being deleted won't cause any conflict.
     *
     * @param string $relation The relation name.
     *
     * @throws RelationDeletionRestrict
     */
    public function restrict(string $relation): void
    {
        if ($this->{$relation}()->exists()) {
            throw new RelationDeletionRestrict($this, $relation);
        }
    }
}
