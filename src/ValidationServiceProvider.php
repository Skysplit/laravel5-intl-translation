<?php

declare(strict_types=1);

namespace Skysplit\Laravel\Translation;

use Illuminate\Validation\ValidationServiceProvider as LaravelProvider;

class ValidationServiceProvider extends LaravelProvider
{
    public function boot()
    {
        app('validator')->resolver(function ($translator, $data, $rules, $messages, $customAttributes) {
            return new Validator($translator, $data, $rules, $messages, $customAttributes);
        });
    }

    /**
     * Register the validation factory.
     */
    protected function registerValidationFactory()
    {
        $this->app->singleton('validator', function ($app) {
            $validator = new ValidationFactory($app['translator'], $app);

            // The validation presence verifier is responsible for determining the existence of
            // values in a given data collection which is typically a relational database or
            // other persistent data stores. It is used to check for "uniqueness" as well.
            if (isset($app['db'], $app['validation.presence'])) {
                $validator->setPresenceVerifier($app['validation.presence']);
            }

            return $validator;
        });
    }
}
