<?php

declare(strict_types=1);

namespace Skysplit\Laravel\Translation;

use Illuminate\Translation\TranslationServiceProvider as LaravelProvider;

class TranslationServiceProvider extends LaravelProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->mergeConfigFrom($this->getConfigPath() . 'translator.php', 'translator');

        $this->registerLoader();

        $this->app->singleton('translator', function ($app) {
            $loader = $app['translation.loader'];

            // When registering the translator component, we'll need to set the default
            // locale as well as the fallback locale. So, we'll grab the application
            // configuration so we can easily get both of these values from there.
            $locale = $app['config']['app.locale'];

            $trans = new Translator($loader, $locale);

            $trans->setFallback($app['config']['app.fallback_locale']);
            $trans->setRegion($app['config']['translator.region']);

            return $trans;
        });
    }

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $this->publishConfig();
        $this->publishLangFiles();
    }

    /**
     * * Publish config files using php artisan vendor:publish.
     */
    protected function publishConfig()
    {
        $configFile = 'translator.php';

        $publishes = [
            $this->getConfigPath() . $configFile => config_path($configFile),
        ];

        $this->publishes($publishes, 'config');
    }

    /**
     * Publish lang files using php artisan vendor:publish.
     */
    protected function publishLangFiles()
    {
        $langPath = $this->getLangPath();

        $locales = [
            'en' => ['validation.php', 'auth.php'],
            'pl' => ['validation.php', 'auth.php', 'passwords.php', 'pagination.php'],
        ];

        foreach ($locales as $locale => $files) {
            $localePath = $langPath . $locale . \DIRECTORY_SEPARATOR;
            $resourcePath = resource_path('lang' . \DIRECTORY_SEPARATOR . $locale) . \DIRECTORY_SEPARATOR;
            $publishes = [];

            foreach ($files as $file) {
                $publishes[$localePath . $file] = $resourcePath . $file;
            }

            $group = 'lang.' . $locale;

            $this->publishes($publishes, $group);
        }
    }

    /**
     * Get config files directory.
     *
     * @return string
     */
    protected function getConfigPath()
    {
        return __DIR__ . \DIRECTORY_SEPARATOR . '..' . \DIRECTORY_SEPARATOR . 'config' . \DIRECTORY_SEPARATOR;
    }

    /**
     * Get language files directory.
     *
     * @return string
     */
    protected function getLangPath()
    {
        return __DIR__ . \DIRECTORY_SEPARATOR . '..' . \DIRECTORY_SEPARATOR . 'resources' . \DIRECTORY_SEPARATOR . 'lang' . \DIRECTORY_SEPARATOR;
    }
}
