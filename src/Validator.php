<?php

namespace Skysplit\Laravel\Translation;

use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Validation\Validator as LaravelValidator;

class Validator extends LaravelValidator
{
    /**
     * {@inheritdoc}
     */
    protected function addError($attribute, $rule, $parameters)
    {
        $message = $this->getMessage($attribute, $rule);
        $message = $this->doReplacements($message, $attribute, $rule, $parameters);

        $this->messages->add($attribute, $message);
    }

    /**
     * {@inheritdoc}
     */
    protected function getMessage($attribute, $rule)
    {
        $lowerRule = Str::snake($rule);
        $inlineMessage = $this->getInlineMessage($attribute, $lowerRule);

        if (! is_null($inlineMessage)) {
            return $inlineMessage;
        }

        $customKey = "validation.custom.{$attribute}.{$lowerRule}";

        $customMessage = $this->getCustomMessageFromTranslator($customKey);

        if ($customMessage !== $customKey) {
            return $customMessage;
        }

        if (in_array($rule, $this->sizeRules)) {
            return $this->getSizeMessage($attribute, $rule);
        }

        $key = "validation.{$lowerRule}";

        if ($this->translator->has($key)) {
            return $this->translator->get($key);
        }

        return $this->getInlineMessage($attribute, $lowerRule, $this->fallbackMessages) ?: $key;
    }

    /**
     * {@inheritdoc}
     */
    protected function doReplacements($message, $attribute, $rule, $parameters)
    {
        if (isset($this->replacers[Str::snake($rule)])) {
            $parameters = $this->callReplacer($message, $attribute, Str::snake($rule), $parameters);
        } elseif (method_exists($this, $replacer = "replace{$rule}")) {
            $parameters = $this->$replacer($message, $attribute, $rule, $parameters);
        }

        $parameters['attribute'] = $this->getAttribute($attribute);

        return $this->translator->formatMessage(null, $message, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    protected function getCustomMessageFromTranslator($customKey)
    {
        if ($this->translator->has($customKey)) {
            return $this->translator->get($customKey);
        }

        $shortKey = preg_replace('/^validation\.custom\./', '', $customKey);

        $customMessages = Arr::dot($this->translator->get('validation.custom'));

        foreach ($customMessages as $key => $message) {
            if (Str::contains($key, ['*']) && Str::is($key, $shortKey)) {
                return $message;
            }
        }

        return $customKey;
    }

    /**
     * {@inheritdoc}
     */
    protected function getSizeMessage($attribute, $rule)
    {
        $lowerRule = Str::snake($rule);
        $type = $this->getAttributeType($attribute);

        $key = "validation.{$lowerRule}.{$type}";

        return $this->translator->get($key);
    }

    /**
     * {@inheritdoc}
     */
    protected function replaceBetween($message, $attribute, $rule, $parameters)
    {
        return [
            'min' => $parameters[0],
            'max' => $parameters[1],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function replaceDateFormat($message, $attribute, $rule, $parameters)
    {
        return [
            'format' => $parameters[0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function replaceDigits($message, $attribute, $rule, $parameters)
    {
        return [
            'digits' => $parameters[0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function replaceMin($message, $attribute, $rule, $parameters)
    {
        return [
            'min' => $parameters[0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function replaceMax($message, $attribute, $rule, $parameters)
    {
        return [
            'max' => $parameters[0],
        ];
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    protected function replaceInArray($message, $attribute, $rule, $parameters)
    {
        return [
            'other' => $this->getAttribute($parameters[0]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function replaceMimes($message, $attribute, $rule, $parameters)
    {
        return [
            'values' => implode(',', $parameters),
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function replaceRequiredWith($message, $attribute, $rule, $parameters)
    {
        return [
            'values' => implode(' / ', $this->getAttributeList($parameters)),
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function replaceSize($message, $attribute, $rule, $parameters)
    {
        return [
            'size' => $parameters[0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function replaceRequiredIf($message, $attribute, $rule, $parameters)
    {
        $parameters[1] = $this->getDisplayableValue($parameters[0], Arr::get($this->data, $parameters[0]));
        $parameters[0] = $this->getAttribute($parameters[0]);

        return [
            'other' => $parameters[0],
            'value' => $parameters[1],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function replaceRequiredUnless($message, $attribute, $rule, $parameters)
    {
        return [
            'other' => $this->getAttribute(array_shift($parameters)),
            'values' => implode(', ', $parameters),
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function replaceSame($message, $attribute, $rule, $parameters)
    {
        return [
            'other' => $this->getAttribute($parameters[0]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function replaceBefore($message, $attribute, $rule, $parameters)
    {
        $date = strtotime($parameters[0]) ? $parameters[0] : $this->getAttribute($parameters[0]);

        return compact('date');
    }
}
