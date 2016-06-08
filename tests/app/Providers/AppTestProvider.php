<?php

class AppTestProvider extends Illuminate\Support\ServiceProvider
{

    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'test');
    }

    public function register()
    {
        // Absolutley nothing
    }

}
