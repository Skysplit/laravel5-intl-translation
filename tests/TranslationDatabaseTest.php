<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Validator;
use test\Models\User;

/**
 * @internal
 * @coversNothing
 */
class TranslationDatabaseTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom([
            '--database' => 'testing',
            '--realpath' => realpath(__DIR__ . '/database/migrations'),
        ]);
    }

    public function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    /**
     * Create test user.
     *
     * @return User
     */
    public function makeUser()
    {
        return User::create(['name' => 'test']);
    }

    public function testDatabaseValidators()
    {
        $this->makeUser();

        $data = [
            'exists' => 2,
            'unique' => 1,
        ];

        $rules = [
            'exists' => 'exists:users,id',
            'unique' => 'unique:users,id',
        ];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());

        $errors = $validator->getMessageBag();

        $this->assertEquals('The selected exists is invalid.', $errors->first('exists'));
        $this->assertEquals('The unique has already been taken.', $errors->first('unique'));
    }
}
