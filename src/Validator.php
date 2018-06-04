<?php

namespace Skysplit\Laravel\Translation;

use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Validation\Validator as LaravelValidator;

class Validator extends LaravelValidator
{
	public function makeReplacements($message, $attribute, $rule, $parameters)
	{
		// Add support for ICU replacement pattern
		// If using this library it makes sense to also convert all validation messages from :attribute to {attribute} in lang files
		// This converts {attribute} back to :attribute so stock replacements are still working
		$message = preg_replace("/{(\w+)}/", ":$1", $message);

		return parent::makeReplacements($message, $attribute, $rule, $parameters);
	}


}
