<?php

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

}
