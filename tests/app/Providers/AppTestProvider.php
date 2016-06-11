<?php

class AppTestProvider extends Illuminate\Support\ServiceProvider
{

    public function boot()
    {
        // Add custom translations vendor
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'test');
    }

    public function register()
    {
        // Absolutely nothing
    }

}
