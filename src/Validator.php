<?php

declare(strict_types=1);

namespace Skysplit\Laravel\Translation;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator as LaravelValidator;

class Validator extends LaravelValidator
{
    public function __construct(Translator $translator, array $data, array $rules,
                                array $messages = [], array $customAttributes = [])
    {
        $this->initialRules = $rules;
        $this->translator = $translator;
        $this->customMessages = $messages;
        $this->data = $this->parseData($data);
        $this->customAttributes = $customAttributes;

        $this->setRules($rules);
    }

    /**
     * Replace all error message place-holders with actual values.
     *
     * @param string $message
     * @param string $attribute
     * @param string $rule
     * @param array  $parameters
     *
     * @return string
     */
    public function makeReplacements($message, $attribute, $rule, $parameters)
    {
        $message = $this->replaceAttributePlaceholder(
            $message, $this->getDisplayableAttribute($attribute)
        );

        $message = $this->replaceInputPlaceholder($message, $attribute);

        if (isset($this->replacers[Str::snake($rule)])) {
            return $this->callReplacer($message, $attribute, Str::snake($rule), $parameters, $this);
        }
        if (method_exists($this, $replacer = "replace{$rule}")) {
            $value = $this->{$replacer}($message, $attribute, $rule, $parameters);
            if (\is_array($value)) {
                return $this->translator->formatMessage(null, $message, array_merge($parameters, $value));
            }

            return $value;
        }

        return $message;
    }

    protected function replaceAttributePlaceholder($message, $value)
    {
        $message = parent::replaceAttributePlaceholder($message, $value);

        return str_replace(
            ['{attribute}', '{ATTRIBUTE}', '{Attribute}'],
            [$value, Str::upper($value), Str::ucfirst($value)],
            $message
        );
    }

    /**
     * Replace all place-holders for the between rule.
     *
     * @param string $message
     * @param string $attribute
     * @param string $rule
     * @param array  $parameters
     *
     * @return array
     */
    protected function replaceBetween($message, $attribute, $rule, $parameters)
    {
        return [
            'min' => $parameters[0],
            'max' => $parameters[1],
        ];
    }

    /**
     * Replace all place-holders for the date_format rule.
     *
     * @param string $message
     * @param string $attribute
     * @param string $rule
     * @param array  $parameters
     *
     * @return array
     */
    protected function replaceDateFormat($message, $attribute, $rule, $parameters)
    {
        return [
            'format' => $parameters[0],
        ];
    }

    /**
     * Replace all place-holders for the different rule.
     *
     * @param string $message
     * @param string $attribute
     * @param string $rule
     * @param array  $parameters
     *
     * @return array
     */
    protected function replaceDifferent($message, $attribute, $rule, $parameters)
    {
        return $this->replaceSame($message, $attribute, $rule, $parameters);
    }

    /**
     * Replace all place-holders for the digits rule.
     *
     * @param string $message
     * @param string $attribute
     * @param string $rule
     * @param array  $parameters
     *
     * @return array
     */
    protected function replaceDigits($message, $attribute, $rule, $parameters)
    {
        return [
            'digits' => $parameters[0],
        ];
    }

    /**
     * Replace all place-holders for the digits (between) rule.
     *
     * @param string $message
     * @param string $attribute
     * @param string $rule
     * @param array  $parameters
     *
     * @return array
     */
    protected function replaceDigitsBetween($message, $attribute, $rule, $parameters)
    {
        return $this->replaceBetween($message, $attribute, $rule, $parameters);
    }

    /**
     * Replace all place-holders for the min rule.
     *
     * @param string $message
     * @param string $attribute
     * @param string $rule
     * @param array  $parameters
     *
     * @return array
     */
    protected function replaceMin($message, $attribute, $rule, $parameters)
    {
        return [
            'min' => $parameters[0],
        ];
    }

    /**
     * Replace all place-holders for the max rule.
     *
     * @param string $message
     * @param string $attribute
     * @param string $rule
     * @param array  $parameters
     *
     * @return array
     */
    protected function replaceMax($message, $attribute, $rule, $parameters)
    {
        return [
            'max' => $parameters[0],
        ];
    }

    /**
     * Replace all place-holders for the in rule.
     *
     * @param string $message
     * @param string $attribute
     * @param string $rule
     * @param array  $parameters
     *
     * @return array
     */
    protected function replaceIn($message, $attribute, $rule, $parameters)
    {
        foreach ($parameters as &$parameter) {
            $parameter = $this->getDisplayableValue($attribute, $parameter);
        }

        return [
            'values' => implode(', ', $parameters),
        ];
    }

    /**
     * Replace all place-holders for the not_in rule.
     *
     * @param string $message
     * @param string $attribute
     * @param string $rule
     * @param array  $parameters
     *
     * @return array
     */
    protected function replaceNotIn($message, $attribute, $rule, $parameters)
    {
        return $this->replaceIn($message, $attribute, $rule, $parameters);
    }

    /**
     * Replace all place-holders for the in_array rule.
     *
     * @param string $message
     * @param string $attribute
     * @param string $rule
     * @param array  $parameters
     *
     * @return array
     */
    protected function replaceInArray($message, $attribute, $rule, $parameters)
    {
        return ['other' => $this->getDisplayableAttribute($parameters[0])];
    }

    /**
     * Replace all place-holders for the mimetypes rule.
     *
     * @param string $message
     * @param string $attribute
     * @param string $rule
     * @param array  $parameters
     *
     * @return array
     */
    protected function replaceMimetypes($message, $attribute, $rule, $parameters)
    {
        return $this->replaceIn($message, $attribute, $rule, $parameters);
    }

    /**
     * Replace all place-holders for the mimes rule.
     *
     * @param string $message
     * @param string $attribute
     * @param string $rule
     * @param array  $parameters
     *
     * @return array
     */
    protected function replaceMimes($message, $attribute, $rule, $parameters)
    {
        return $this->replaceIn($message, $attribute, $rule, $parameters);
    }

    /**
     * Replace all place-holders for the required_with rule.
     *
     * @param string $message
     * @param string $attribute
     * @param string $rule
     * @param array  $parameters
     *
     * @return array
     */
    protected function replaceRequiredWith($message, $attribute, $rule, $parameters)
    {
        return [
            'values' => implode(' / ', $this->getAttributeList($parameters)),
        ];
    }

    /**
     * Replace all place-holders for the required_with_all rule.
     *
     * @param string $message
     * @param string $attribute
     * @param string $rule
     * @param array  $parameters
     *
     * @return array
     */
    protected function replaceRequiredWithAll($message, $attribute, $rule, $parameters)
    {
        return $this->replaceRequiredWith($message, $attribute, $rule, $parameters);
    }

    /**
     * Replace all place-holders for the required_without rule.
     *
     * @param string $message
     * @param string $attribute
     * @param string $rule
     * @param array  $parameters
     *
     * @return array
     */
    protected function replaceRequiredWithout($message, $attribute, $rule, $parameters)
    {
        return $this->replaceRequiredWith($message, $attribute, $rule, $parameters);
    }

    /**
     * Replace all place-holders for the required_without_all rule.
     *
     * @param string $message
     * @param string $attribute
     * @param string $rule
     * @param array  $parameters
     *
     * @return array
     */
    protected function replaceRequiredWithoutAll($message, $attribute, $rule, $parameters)
    {
        return $this->replaceRequiredWith($message, $attribute, $rule, $parameters);
    }

    /**
     * Replace all place-holders for the size rule.
     *
     * @param string $message
     * @param string $attribute
     * @param string $rule
     * @param array  $parameters
     *
     * @return array
     */
    protected function replaceSize($message, $attribute, $rule, $parameters)
    {
        return [
            'size' => $parameters[0],
        ];
    }

    /**
     * Replace all place-holders for the gt rule.
     *
     * @param string $message
     * @param string $attribute
     * @param string $rule
     * @param array  $parameters
     *
     * @return array
     */
    protected function replaceGt($message, $attribute, $rule, $parameters)
    {
        if (($value = $this->getValue($parameters[0])) === null) {
            return [
                'value' => $parameters[0],
            ];
        }

        return [
            'value' => $this->getSize($attribute, $value),
        ];
    }

    /**
     * Replace all place-holders for the lt rule.
     *
     * @param string $message
     * @param string $attribute
     * @param string $rule
     * @param array  $parameters
     *
     * @return array
     */
    protected function replaceLt($message, $attribute, $rule, $parameters)
    {
        if (($value = $this->getValue($parameters[0])) === null) {
            return [
                'value' => $parameters[0],
            ];
        }

        return [
            'value' => $this->getSize($attribute, $value),
        ];
    }

    /**
     * Replace all place-holders for the gte rule.
     *
     * @param string $message
     * @param string $attribute
     * @param string $rule
     * @param array  $parameters
     *
     * @return array
     */
    protected function replaceGte($message, $attribute, $rule, $parameters)
    {
        if (($value = $this->getValue($parameters[0])) === null) {
            return [
                'value' => $parameters[0],
            ];
        }

        return [
            'value' => $this->getSize($attribute, $value),
        ];
    }

    /**
     * Replace all place-holders for the lte rule.
     *
     * @param string $message
     * @param string $attribute
     * @param string $rule
     * @param array  $parameters
     *
     * @return array
     */
    protected function replaceLte($message, $attribute, $rule, $parameters)
    {
        if (($value = $this->getValue($parameters[0])) === null) {
            return [
                'value' => $parameters[0],
            ];
        }

        return [
            'value' => $this->getSize($attribute, $value),
        ];
    }

    /**
     * Replace all place-holders for the required_if rule.
     *
     * @param string $message
     * @param string $attribute
     * @param string $rule
     * @param array  $parameters
     *
     * @return array
     */
    protected function replaceRequiredIf($message, $attribute, $rule, $parameters)
    {
        $parameters[1] = $this->getDisplayableValue($parameters[0], Arr::get($this->data, $parameters[0]));

        $parameters[0] = $this->getDisplayableAttribute($parameters[0]);

        return [
            'other' => $parameters[0],
            'value' => $parameters[1],
        ];
    }

    /**
     * Replace all place-holders for the required_unless rule.
     *
     * @param string $message
     * @param string $attribute
     * @param string $rule
     * @param array  $parameters
     *
     * @return array
     */
    protected function replaceRequiredUnless($message, $attribute, $rule, $parameters)
    {
        $other = $this->getDisplayableAttribute($parameters[0]);

        $values = [];

        foreach (\array_slice($parameters, 1) as $value) {
            $values[] = $this->getDisplayableValue($parameters[0], $value);
        }

        return [
            'other' => $other,
            'values' => implode(', ', $values),
        ];
    }

    /**
     * Replace all place-holders for the same rule.
     *
     * @param string $message
     * @param string $attribute
     * @param string $rule
     * @param array  $parameters
     *
     * @return array
     */
    protected function replaceSame($message, $attribute, $rule, $parameters)
    {
        return [
            'other' => $this->getDisplayableAttribute($parameters[0]),
        ];
    }

    /**
     * Replace all place-holders for the before rule.
     *
     * @param string $message
     * @param string $attribute
     * @param string $rule
     * @param array  $parameters
     *
     * @return array
     */
    protected function replaceBefore($message, $attribute, $rule, $parameters)
    {
        if (!strtotime($parameters[0])) {
            return ['date' => $this->getDisplayableAttribute($parameters[0])];
        }

        return ['date' => $this->getDisplayableValue($attribute, $parameters[0])];
    }

    /**
     * Replace all place-holders for the before_or_equal rule.
     *
     * @param string $message
     * @param string $attribute
     * @param string $rule
     * @param array  $parameters
     *
     * @return array
     */
    protected function replaceBeforeOrEqual($message, $attribute, $rule, $parameters)
    {
        return $this->replaceBefore($message, $attribute, $rule, $parameters);
    }

    /**
     * Replace all place-holders for the after rule.
     *
     * @param string $message
     * @param string $attribute
     * @param string $rule
     * @param array  $parameters
     *
     * @return array
     */
    protected function replaceAfter($message, $attribute, $rule, $parameters)
    {
        return $this->replaceBefore($message, $attribute, $rule, $parameters);
    }

    /**
     * Replace all place-holders for the after_or_equal rule.
     *
     * @param string $message
     * @param string $attribute
     * @param string $rule
     * @param array  $parameters
     *
     * @return array
     */
    protected function replaceAfterOrEqual($message, $attribute, $rule, $parameters)
    {
        return $this->replaceBefore($message, $attribute, $rule, $parameters);
    }

    /**
     * Replace all place-holders for the date_equals rule.
     *
     * @param string $message
     * @param string $attribute
     * @param string $rule
     * @param array  $parameters
     *
     * @return array
     */
    protected function replaceDateEquals($message, $attribute, $rule, $parameters)
    {
        return $this->replaceBefore($message, $attribute, $rule, $parameters);
    }

    /**
     * Replace all place-holders for the dimensions rule.
     *
     * @param string $message
     * @param string $attribute
     * @param string $rule
     * @param array  $parameters
     *
     * @return string
     */
    protected function replaceDimensions($message, $attribute, $rule, $parameters)
    {
        $parameters = $this->parseNamedParameters($parameters);

        if (\is_array($parameters)) {
            foreach ($parameters as $key => $value) {
                $message = str_replace('{}' . $key, $value, $message);
            }
        }

        return $message;
    }

    /**
     * Replace all place-holders for the starts_with rule.
     *
     * @param string $message
     * @param string $attribute
     * @param string $rule
     * @param array  $parameters
     *
     * @return array
     */
    protected function replaceStartsWith($message, $attribute, $rule, $parameters)
    {
        foreach ($parameters as &$parameter) {
            $parameter = $this->getDisplayableValue($attribute, $parameter);
        }

        return ['values' => implode(', ', $parameters)];
    }
}
