<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Models\Traits;

use Illuminate\Support\Facades\Validator;
use Mathrix\Lumen\Zero\Exceptions\Validation;

/**
 * Trait HasValidator.
 */
trait HasValidator
{
    /** @var array The Validation rules */
    protected $rules = [];

    /**
     * The HashValidator boot function.
     */
    protected static function bootHasValidator()
    {
        static::saving(static function (self $model) {
            $model->validate();
        });
    }

    /**
     * Validate model data after attributes mutation.
     *
     * @throws Validation
     */
    public function validate()
    {
        $validator = Validator::make($this->getValidationData(), $this->getValidationRules());

        if ($validator->fails()) {
            throw new Validation($validator->errors()->getMessages());
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
