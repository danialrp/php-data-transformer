<?php

namespace DanialPanah\DataTransformer\Validations;


use BadMethodCallException;
use DanialPanah\DataTransformer\Exceptions\ValidationException;
use DateTimeImmutable;

class Validation
{
    const VALIDATION_MESSAGE = 'Validation Failed: ';


    public function __call($method, $args)
    {
        if (str_contains($method, '.in')) {
            $subValueEnums = $this->inSubValueEnums($method, $args);
            $this->in($subValueEnums['attribute'], $subValueEnums['value'], $subValueEnums['rule']);
        } else throw new BadMethodCallException();
    }

    public function passes(string $attribute, mixed $value, array $rules): void
    {
        foreach ($rules as $rule) {
            $ruleMethod = strtok($rule, ':');
            $this->$ruleMethod($attribute, $value, $rules);
        }
    }

    private function inSubValueEnums($method, $args): array
    {
        $ruleKey = str_replace('.in', '', $method);
        $attribute = $args[0] . '.' . $ruleKey;
        $value = $args[1][$ruleKey];
        $rule = [];

        foreach ($args[2] as $ruleValue)
            if (str_contains($ruleValue, $method))
                $rule [] = str_replace($method, 'in', $ruleValue);

        return ['attribute' => $attribute, 'value' => $value, 'rule' => $rule];
    }

    private function extractRequiredDataFromRules(array $rules, string $methodName): array
    {
        foreach ($rules as $rule)
            if (str_contains($rule, $methodName . ':'))
                $requiredData = str_replace($methodName . ':', '', $rule);

        return str_replace(' ', '', explode(',', $requiredData));
    }

    private function required($attribute, $value, $rules): void
    {
        if (!isset($attribute) || $value == null || empty($value))
            throw new ValidationException(self::VALIDATION_MESSAGE . "The '{$attribute}' field is required.");
    }

    private function boolean($attribute, $value, $rules): void
    {
        if ($value === NULL) return;

        if (filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === NULL)
            throw new ValidationException(self::VALIDATION_MESSAGE . "'{$attribute}' must be a boolean value.");
    }

    private function dateTime($attribute, $value, $rules): void
    {
        if (!isset($value)) return;

        $format = 'Y-m-d';
        $simpleDate = DateTimeImmutable::createFromFormat($format, $value);
        $atomDate = DateTimeImmutable::createFromFormat(DATE_ATOM, $value);
        $errorMessage = self::VALIDATION_MESSAGE . "'{$attribute}' format is not valid. use [Y-m-d] or Atomic format.";

        if (!$simpleDate && !$atomDate)
            throw new ValidationException($errorMessage);

        if ($simpleDate && $simpleDate->format($format) !== $value)
            throw new ValidationException($errorMessage);

        if ($atomDate && $atomDate->format('c') !== $value)
            throw new ValidationException($errorMessage);
    }

    private function string($attribute, $value, $rules): void
    {
        if (!isset($value)) return;

        if (!is_string($value) || is_int($value) || is_float($value) || is_bool($value))
            throw new ValidationException(self::VALIDATION_MESSAGE . "'{$attribute}' must be a valid string.");
    }

    private function array($attribute, $value, $rules): void
    {
        if (!isset($value)) return;

        if (!is_array($value) || !count($value))
            throw new ValidationException(self::VALIDATION_MESSAGE . "'{$attribute}' must be a valid array.");
    }

    private function array_keys($attribute, $value, $rules): void
    {
        if (!isset($value)) return;

        if (!in_array('array', $rules)) return;

        $requiredKeys = $this->extractRequiredDataFromRules($rules, __FUNCTION__);

        foreach ($requiredKeys as $requiredKey) {
            if (array_key_exists($requiredKey, $value)) continue;
            foreach ($value as $item) {
                if (!is_array($item)) throw new ValidationException(self::VALIDATION_MESSAGE . "The '{$requiredKey}' key must be presented in '{$attribute}' array.");
                if (!array_key_exists($requiredKey, $item))
                    throw new ValidationException(self::VALIDATION_MESSAGE . "The '{$requiredKey}' key must be presented in '{$attribute}' array.");
            }
        }
    }

    private function in($attribute, $value, $rules): void
    {
        if (!isset($value)) return;

        $allowedValues = $this->extractRequiredDataFromRules($rules, __FUNCTION__);
        $errorMessage = self::VALIDATION_MESSAGE . "Allowed values for '{$attribute}' are: " . implode(', ', $allowedValues);

        if (is_array($value)) {
            foreach ($value as $item)
                if (!in_array($item, $allowedValues))
                    throw new ValidationException($errorMessage);
        } elseif (!in_array($value, $allowedValues))
            throw new ValidationException($errorMessage);
    }
}