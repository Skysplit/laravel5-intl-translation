<?php

use Skysplit\Laravel\Translation\TranslationServiceProvider;
use Skysplit\Laravel\Translation\Translator;
use Skysplit\Laravel\Translation\ValidationServiceProvider;

class TestCase extends Orchestra\Testbench\TestCase
{

    /**
     * @var string|null
     */
    protected $fixturesPath = null;

    public function setUp() :void
    {
        parent::setUp();
        
        $this->fixturesPath = __DIR__ . '/fixtures/files';
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup locale
        $app['config']->set('locale', 'en');
        
        // Set lang files path
        $app['path.lang'] = __DIR__ . '/../resources/lang';
        
        // Reset app base path, as it is set to orchestra vendor directory
        $app['path.base'] = __DIR__ . '/../src';
    }

    protected function getPackageProviders($app)
    {
        return [
            AppTestProvider::class,
            TranslationServiceProvider::class,
            ValidationServiceProvider::class,
        ];
    }

}
