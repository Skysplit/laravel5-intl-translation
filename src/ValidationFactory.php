<?php


namespace Skysplit\Laravel\Translation;


use App\App;
use Illuminate\Validation\Factory;

class ValidationFactory extends Factory
{
    public function __construct(Translator $translator, \Illuminate\Foundation\Application $container = null)
    {
        $this->container = $container;
        $this->translator = $translator;
    }
}