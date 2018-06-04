<?php

namespace Skysplit\Laravel\Translation;

use Countable;
use MessageFormatter;
use Illuminate\Support\Arr;
use Illuminate\Contracts\Translation\Loader;
use Illuminate\Support\NamespacedItemResolver;

class Translator extends \Illuminate\Translation\Translator implements \Illuminate\Contracts\Translation\Translator
{

	/**
	 * Locale region used by translator
	 *
	 * @var string
	 */
	protected $region;

	/**
	 * Get the translation for the given key.
	 *
	 * @param  string  $key
	 * @param  array   $replace
	 * @param  string|null  $locale
	 * @param  bool  $fallback
	 * @return string|array|null
	 */
	public function get($key, array $replace = [], $locale = null, $fallback = true)
	{
		list($namespace, $group, $item) = $this->parseKey($key);

		$locales = $fallback ? $this->parseLocale($locale) : [$locale ? : $this->locale];

		foreach ($locales as $locale) {
			$this->load($namespace, $group, $locale);

			$message = $this->getLine($namespace, $group, $locale, $item);

			if (!is_null($message)) {
				break;
			}
		}

		if (!isset($message)) {
			return $key;
		}

		return $message;
	}

	/**
	 * Retrieve a language line out the loaded array.
	 *
	 * @param  string  $namespace
	 * @param  string  $group
	 * @param  string  $locale
	 * @param  string  $item
	 * @return string|null
	 * * @return string|array|null
	 */
	protected function getLine($namespace, $group, $locale, $item, array $replace = [])
	{
		$line = Arr::get($this->loaded[$namespace][$group][$locale], $item);

		if (is_string($line) || (is_array($line) && count($line) > 0)) {
			return $line;
		}
	}

	/**
	 * Formats message using php MessageFormatter::formatMessage method
	 *
	 * @param string $locale
	 * @param string $message
	 * @param array $parameters
	 * @return string
	 */
	public function formatMessage($locale, $message, array $parameters)
	{
		// Fake parameters to avoid non-matching arguments to be replaced with {0}
		$parameters["__"] = "__";
		return MessageFormatter::formatMessage($this->getLocaleRegion($locale), $message, $parameters);
	}

	/**
	 * Translates the given message.
	 *
	 * @param string      $id         The message id (may also be an object that can be cast to string)
	 * @param array       $parameters An array of parameters for the message
	 * @param string|null $locale     The locale or null to use the default
	 *
	 * @return string The translated string
	 */
	public function trans($id, array $parameters = [], $locale = null)
	{
		return $this->formatMessage($locale, $this->get($id, [], $locale), $parameters);
	}

	/**
	 * Translates the given choice message by choosing a translation according to a number.
	 *
	 * @param string      $id         The message id (may also be an object that can be cast to string)
	 * @param int         $number     The number to use to find the indice of the message
	 * @param array       $parameters An array of parameters for the message
	 * @param string|null $locale     The locale or null to use the default
	 *
	 * @return string The translated string
	 */
	public function transChoice($id, $number, array $parameters = [], $locale = null)
	{
		if (is_array($number) || $number instanceof Countable) {
			$number = count($number);
		}

		$parameters = array_merge($parameters, ['n' => $number]);

		return $this->trans($id, $parameters, $locale);
	}




	/**
	 * Get the array of locales to be checked.
	 *
	 * @param  string|null  $locale
	 * @return array
	 */
	protected function parseLocale($locale)
	{
		return array_filter([$locale ? : $this->locale, $this->fallback]);
	}


	/**
	 * Set locale region
	 *
	 * @param string $region
	 */
	public function setRegion($region)
	{
		$this->region = $region;
	}

	/**
	 * Get locale region
	 *
	 * @return string|null
	 */
	public function getRegion()
	{
		return $this->region;
	}

	/**
	 * Get locale with region separated by hypen
	 *
	 * @param string|null $locale
	 * @param string|null $region
	 * @return string
	 */
	public function getLocaleRegion($locale = null, $region = null)
	{
		$locale = $locale ? : ($this->getLocale() ? : $this->getFallback());
		$region = $region ? : $this->getRegion();

		if ($region) {
			$locale .= '-' . $region;
		}

		return $locale;
	}



}
