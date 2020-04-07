<?php

declare(strict_types=1);

namespace Skysplit\Laravel\Translation;

use Countable;
use Illuminate\Contracts\Translation\Loader;
use Illuminate\Contracts\Translation\Translator as TranslatorContract;
use Illuminate\Support\Arr;
use Illuminate\Support\NamespacedItemResolver;
use MessageFormatter;

class Translator extends NamespacedItemResolver implements TranslatorContract
{
    private const DUMMY_PARAMETER_KEY = '__';

    /**
     * The loader implementation.
     *
     * @varLoader
     */
    protected $loader;

    /**
     * The default locale being used by the translator.
     *
     * @var string
     */
    protected $locale;

    /**
     * The fallback locale used by the translator.
     *
     * @var string
     */
    protected $fallback;

    /**
     * Locale region used by translator.
     *
     * @var string
     */
    protected $region;

    /**
     * The array of loaded translation groups.
     *
     * @var array
     */
    protected $loaded = [];

    /**
     * Create a new translator instance.
     *
     * @param string $locale
     */
    public function __construct(Loader $loader, $locale)
    {
        $this->loader = $loader;
        $this->setLocale($locale);
    }

    /**
     * Determine if a translation exists.
     *
     * @param string      $key
     * @param null|string $locale
     * @param bool        $fallback
     *
     * @return bool
     */
    public function has($key, $locale = null)
    {
        return $this->getMessage($key, $locale) !== $key;
    }

    /**
     * Formats message using php MessageFormatter::formatMessage method.
     */
    public function formatMessage(?string $locale, string $message, array $parameters): string
    {
        if (isset($parameters[self::DUMMY_PARAMETER_KEY]) && \count($parameters) === 1) {
            return $message;
        }

        return MessageFormatter::formatMessage($this->getLocaleRegion($locale), $message, $parameters);
    }

    /**
     * Translates the given message.
     *
     * @param string      $id         The message id (may also be an object that can be cast to string)
     * @param array       $parameters An array of parameters for the message
     * @param null|string $locale     The locale or null to use the default
     *
     * @return array|string The translated string
     */
    public function get($id, array $parameters = [], $locale = null)
    {
        // for older versions of the intl-package we must provide a non-empty array with a dummy value ["___"] to prevent
        // the placeholder to be replaced by {0}
        $parameters[self::DUMMY_PARAMETER_KEY] = '__';
        $message = $this->getMessage($id, $locale);

        // For custom-validation-messages we need to be able to return an array
        if (\is_array($message)) {
            return $message;
        }

        return $this->formatMessage($locale, $message, $parameters);
    }

    /**
     * Translates the given choice message by choosing a translation according to a number.
     *
     * @param string      $id         The message id (may also be an object that can be cast to string)
     * @param int         $number     The number to use to find the indice of the message
     * @param array       $parameters An array of parameters for the message
     * @param null|string $locale     The locale or null to use the default
     *
     * @return string The translated string
     */
    public function transChoice($id, $number, array $parameters = [], $locale = null): string
    {
        if (\is_array($number) || $number instanceof Countable) {
            $number = \count($number);
        }

        $parameters = array_merge($parameters, ['n' => $number]);

        return $this->get($id, $parameters, $locale);
    }

    public function choice($key, $number, array $replace = [], $locale = null)
    {
        return $this->transChoice($key, $number, $replace, $locale);
    }

    /**
     * Get the language line loader implementation.
     */
    public function getLoader(): Loader
    {
        return $this->loader;
    }

    /**
     * Set the default locale.
     *
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * Get the default locale being used.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Get the default locale being used.
     *
     * @return string
     */
    public function locale()
    {
        return $this->getLocale();
    }

    /**
     * Set the fallback locale being used.
     *
     * @param string $fallback
     */
    public function setFallback($fallback)
    {
        $this->fallback = $fallback;
    }

    /**
     * Get the fallback locale being used.
     *
     * @return string
     */
    public function getFallback()
    {
        return $this->fallback;
    }

    /**
     * Set locale region.
     *
     * @param string $region
     */
    public function setRegion($region)
    {
        $this->region = $region;
    }

    /**
     * Get locale region.
     *
     * @return null|string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Get locale with region separated by hypen.
     *
     * @param null|string $locale
     * @param null|string $region
     *
     * @return string
     */
    public function getLocaleRegion($locale = null, $region = null)
    {
        $locale = $locale ?: ($this->getLocale() ?: $this->getFallback());
        $region = $region ?: $this->getRegion();

        if ($region) {
            $locale .= '-' . $region;
        }

        return $locale;
    }

    /**
     * Parse a key into namespace, group, and item.
     *
     * @param string $key
     *
     * @return array
     */
    public function parseKey($key)
    {
        $segments = parent::parseKey($key);

        if ($segments[0] === null) {
            $segments[0] = '*';
        }

        return $segments;
    }

    /**
     * Load the specified language group.
     *
     * @param string $namespace
     * @param string $group
     * @param string $locale
     */
    public function load($namespace, $group, $locale)
    {
        if ($this->isLoaded($namespace, $group, $locale)) {
            return;
        }

        // The loader is responsible for returning the array of language lines for the
        // given namespace, group, and locale. We'll set the lines in this array of
        // lines that have already been loaded so that we can easily access them.
        $lines = $this->loader->load($locale, $group, $namespace);

        $this->loaded[$namespace][$group][$locale] = $lines;
    }

    /**
     * Add a new namespace to the loader.
     *
     * @param string $namespace
     * @param string $hint
     */
    public function addNamespace($namespace, $hint)
    {
        $this->loader->addNamespace($namespace, $hint);
    }

    /**
     * Get the translation for the given key.
     *
     * @param string      $key
     * @param null|string $locale
     * @param bool        $fallback
     *
     * @return null|array|string
     */
    public function getMessage($key, $locale = null)
    {
        [$namespace, $group, $item] = $this->parseKey($key);

        $locales = [$locale ?: $this->locale];

        foreach ($locales as $locale) {
            $this->load($namespace, $group, $locale);

            $message = $this->getLine($namespace, $group, $locale, $item);

            if ($message !== null) {
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
     * @param string $namespace
     * @param string $group
     * @param string $locale
     * @param string $item
     *
     * @return null|array|string
     */
    protected function getLine($namespace, $group, $locale, $item)
    {
        $line = Arr::get($this->loaded[$namespace][$group][$locale], $item);

        if (\is_string($line) || (\is_array($line) && \count($line) > 0)) {
            return $line;
        }

        return null;
    }

    /**
     * Get the array of locales to be checked.
     *
     * @param null|string $locale
     *
     * @return array
     */
    protected function parseLocale($locale)
    {
        return array_filter([$locale ?: $this->locale, $this->fallback]);
    }

    /**
     * Determine if the given group has been loaded.
     *
     * @param string $namespace
     * @param string $group
     * @param string $locale
     *
     * @return bool
     */
    protected function isLoaded($namespace, $group, $locale)
    {
        return isset($this->loaded[$namespace][$group][$locale]);
    }
}
