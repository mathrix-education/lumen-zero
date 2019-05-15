<?php

namespace Mathrix\Lumen\Zero\Controllers\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Mathrix\Lumen\Zero\Exceptions\ValidationException;

/**
 * Trait HasRequestValidator.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 2.0.0
 */
trait HasRequestValidator
{
    /**
     * Validate the given request with the given rules.
     *
     * @param Request $request
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     *
     * @return array
     * @throws ValidationException
     */
    public function validate(Request $request, array $rules, array $messages = [], array $customAttributes = [])
    {
        $validator = ValidatorFacade::make($request->all(), $rules, $messages, $customAttributes);

        if ($validator->fails()) {
            $errors = $validator->errors()->getMessages();
            throw new ValidationException($errors, "Model data failed to pass validation.");
        }

        return $this->extractInputFromRules($request, $rules);
    }
}
