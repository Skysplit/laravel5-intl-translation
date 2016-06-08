<?php

use Skysplit\Laravel\Translation\TranslationServiceProvider;
use Skysplit\Laravel\Translation\ValidationServiceProvider;

class TestCaste extends Orchestra\Testbench\TestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('locale', 'en');
    }

    protected function getPackageProviders($app)
    {
        return [
            AppTestProvider::class,
            TranslationServiceProvider::class,
            ValidationServiceProvider::class,
        ];
    }
    
    public function testAttributesPlaceholder()
    {
        $this->assertEquals('Hello, Jane!', trans('test::test.hello', ['name' => 'Jane']));
        $this->assertEquals('Hello, Jon!', trans('test::test.hello', ['name' => 'Jon']));
    }

    public function testPlurals()
    {
        $this->assertEquals('no apples', trans_choice('test::test.apples', 0));
        $this->assertEquals('1 apple', trans_choice('test::test.apples', 1));
        $this->assertEquals('2 apples', trans_choice('test::test.apples', 2));
    }
    
    public function testPluralsOffset()
    {
        $this->assertEquals('You do not like this yet', trans_choice('test::test.offset', 0));
        $this->assertEquals('You liked this', trans_choice('test::test.offset', 1));
        $this->assertEquals('You and one other person liked this', trans_choice('test::test.offset', 2));
        $this->assertEquals('You and 2 others liked this', trans_choice('test::test.offset', 3));
    }
    
    public function testSelect()
    {
        $this->assertEquals('He has two legs and is male!', trans('test::test.select', ['gender' => 'male']));
        $this->assertEquals('She has two legs and is female!', trans('test::test.select', ['gender' => 'female']));
        $this->assertEquals('It has two legs and is penguin!', trans('test::test.select', ['gender' => 'penguin']));
    }

}
