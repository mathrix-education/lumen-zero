<?php

namespace Mathrix\Lumen\Bases;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Mathrix\Lumen\Exceptions\ValidationException;

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
    /** @var bool Use appends */
    public static $withAppends = true;

    /** @var bool Indicates if the model should be timestamped */
    public $timestamps = true;

    /** @var bool Validate before save */
    protected $validate = true;
    /** @var array Default validation rules */
    protected $rules = [];
    /** @var string Validation rules to use */
    protected $useRules = "rules";
    /** @var bool|array Validation errors */
    protected $errors = false;


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
            if (!\is_array($conditions[0])) {
                $conditions = [$conditions];
            }

            $query = $query->where($conditions);
        }

        return $query->firstOrFail();
    }


    /**
     * Use appends.
     */
    public static function withAppends()
    {
        self::$withAppends = true;

        return new static();
    }


    /**
     * Don"t use appends.
     */
    public static function withoutAppends()
    {
        self::$withAppends = false;

        return new static();
    }


    /**
     * Un-guard some attributes.
     *
     * @param string|array $attributes
     *
     * @return $this
     */
    public function unguardAttributes($attributes)
    {
        if (\is_string($attributes)) {
            $attributes = [$attributes];
        }

        if (!empty($this->fillable)) {
            $this->fillable = array_merge($this->fillable, $attributes);
        } else {
            $this->guarded = array_diff($this->guarded, $attributes);
        }

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Response
    |--------------------------------------------------------------------------
    */

    /**
     * Override to inject systematic validation and exception.
     *
     * @param array $options
     *
     * @throws ValidationException
     *
     * @return bool
     */
    public function save(array $options = [])
    {
        $query = $this->newQueryWithoutScopes();

        // If the "saving" event returns false we"ll bail out of the save and return
        // false, indicating that the save failed. This provides a chance for any
        // listeners to cancel save operations if validations fail or whatever.
        if ($this->fireModelEvent("saving") === false) {
            return false;
        }

        if ($this->validateOnSave() && !$this->validate()) {
            throw new ValidationException($this->errors, "Model data failed to pass validation.");
        }

        if ($this->exists) {
            // If the model already exists in the database we can just update our record
            // that is already in this database using the current IDs in this "where"
            // clause to only update this model. Otherwise, we"ll just insert them.
            $saved = !$this->isDirty() || $this->performUpdate($query);
        } else {
            // If the model is brand new, we"ll insert it into our database and set the
            // ID attribute on the model to the value of the newly inserted row"s ID
            // which is typically an auto-increment value managed by the database.
            $saved = $this->performInsert($query);

            if (!$this->getConnectionName() &&
                $connection = $query->getConnection()) {
                $this->setConnection($connection->getName());
            }
        }

        // If the model is successfully saved, we need to do a few more things once
        // that is done. We will call the "saved" method here to run any actions
        // we need to happen after a model gets successfully saved right here.
        if ($saved) {
            $this->finishSave($options);
        }

        return $saved;
    }

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    /**
     * @return bool If model need to be validated before save
     */
    public function validateOnSave()
    {
        return $this->validate;
    }


    /**
     * Validate model data.
     *
     * @param string $rules
     *
     * @return bool
     */
    public function validate($rules = "rules")
    {
        if (\is_string($rules)) {
            $this->useRules = $rules;
        }

        $data = Arr::only($this->toArray(), $this->fillable);
        $rules = $this->{$this->useRules};

        foreach ($rules as $ruleName => $rule) {
            $dataKeys = array_keys($data);
            $found = false;

            foreach ($dataKeys as $dataKey) {
                if (str_start($ruleName, $dataKey)) {
                    $found = true;
                }
            }

            if (!$found) {
                unset($rules[$ruleName]);
            }
        }

        $validator = ValidatorFacade::make($data, $rules);
        $validator = $this->injectInValidator($validator);

        if ($validator->fails()) {
            $this->errors = $validator->errors()->getMessages();

            return false;
        }

        return true;
    }


    /**
     * Inject custom rules in the validator before save.
     *
     * @param Validator $validator
     *
     * @return Validator
     */
    public function injectInValidator($validator)
    {
        return $validator;
    }


    /**
     * Enable the model validation.
     */
    public function enableValidation()
    {
        $this->validate = true;
    }


    /**
     * Disable the model validation.
     */
    public function disableValidation()
    {
        $this->validate = false;
    }


    /**
     * Returns the validation errors.
     *
     * @return bool|array
     */
    public function errors()
    {
        return $this->errors;
    }
}
