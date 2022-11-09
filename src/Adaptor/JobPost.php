<?php

namespace DanialPanah\DataTransformer\Adaptor;

use DanialPanah\DataTransformer\Contracts\Validatable;
use JsonSerializable;

abstract class JobPost implements JsonSerializable, Validatable
{
    public function passRequiredProperties(object $validatable): void
    {
        $validatableRules = $validatable->rules;

        foreach ($validatableRules as $attribute => $rule) {
            if (!in_array('required', $rule)) continue;
            $validatable->validation->passes($attribute, $validatable->$attribute, ['required']);
        }
    }
}