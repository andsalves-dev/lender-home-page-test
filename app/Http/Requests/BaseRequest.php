<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

abstract class BaseRequest extends FormRequest {

    protected function failedValidation(Validator $validator) {
        return null;
    }

    public function getValidator() {
        return parent::getValidatorInstance();
    }
}
