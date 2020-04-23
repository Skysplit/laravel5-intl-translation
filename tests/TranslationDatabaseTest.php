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
            '--path' => realpath(__DIR__ . '/database/migrations'),
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

    public function testDatabaseValidators(): void
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

    /**
     * Create test user.
     *
     * @return User
     */
    private function makeUser()
    {
        return User::create(['name' => 'test']);
    }
}
