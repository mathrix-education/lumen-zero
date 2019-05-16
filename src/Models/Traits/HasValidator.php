<?php

namespace Mathrix\Lumen\Zero\Models\Traits;

use Illuminate\Support\Facades\Validator;
use Mathrix\Lumen\Zero\Exceptions\ValidationException;

/**
 * Trait HasValidator.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
trait HasValidator
{
    /** @var array The Validation rules */
    protected $rules = [];
    /** @var bool|array The validation errors */
    protected $validationErrors = [];


    /**
     * The HashValidator boot function.
     */
    protected static function bootHasValidator()
    {
        static::saving(function (self $model) {
            $model->validate();
        });
    }


    /**
     * Validate model data after attributes mutation.
     *
     * @throws ValidationException
     */
    public function validate()
    {
        $validator = Validator::make($this->getValidationData(), $this->getValidationRules());

        if ($validator->fails()) {
            throw new ValidationException($validator->errors()->getMessages());
        }

        return true;
    }


    /**
     * Get the validation data. May be overridden if the model requires specific logic.
     *
     * @return array
     */
    protected function getValidationData()
    {
        $hidden = $this->getHidden();
        $this->setHidden([]);
        $data = $this->toArray();
        $this->setHidden($hidden);

        return $data;
    }


    /**
     * Get the validation rules. May be overridden if the model requires specific logic.
     *
     * @return array
     */
    protected function getValidationRules()
    {
        return $this->rules;
    }
}
