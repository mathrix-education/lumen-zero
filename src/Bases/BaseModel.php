<?php

namespace Mathrix\Lumen\Bases;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Mathrix\Lumen\Models\Traits\HasValidator;

/**
 * Class BaseModel.
 * Base for all models, implement validation on save useless manual disable.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 *
 * @property int|mixed $id
 */
abstract class BaseModel extends Model
{
    use HasValidator;


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
     * @param array $conditions The conditions
     *
     * @return Builder|BaseModel|Model|static
     */
    public static function random($conditions = [])
    {
        $query = self::query()->inRandomOrder();

        if (!empty($conditions)) {
            if (!is_array($conditions[0])) {
                $conditions = [$conditions];
            }

            $query = $query->where($conditions);
        }

        return $query->firstOrFail();
    }


    /**
     * Set default date format to be in compliance with the OpenAPI date-time format.
     *
     * @param DateTimeInterface $date The DateTimeInterface.
     * @return string The serialized date.
     *
     * @link https://swagger.io/docs/specification/data-models/data-types/#string
     * @link https://tools.ietf.org/html/rfc3339#section-5.6
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return Carbon::instance($date)->format("c");
    }
}
