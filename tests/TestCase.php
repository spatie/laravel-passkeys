<?php

namespace Spatie\LaravelPasskeys\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\LaravelPasskeys\LaravelPasskeysServiceProvider;
use Spatie\LaravelPasskeys\Tests\TestSupport\Models\User;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Str::createRandomStringsUsing(function () {
            return 'fake-random-string';
        });

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Spatie\\LaravelPasskeys\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            LivewireServiceProvider::class,
            LaravelPasskeysServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('auth.providers.users.model', User::class);
        config()->set('passkeys.models.authenticatable', User::class);

        $migration = include __DIR__.'/../vendor/orchestra/testbench-core/laravel/migrations/0001_01_01_000000_testbench_create_users_table.php';
        $migration->up();

        $migration = include __DIR__.'/../database/migrations/create_passkeys_table.php.stub';
        $migration->up();
    }
}
