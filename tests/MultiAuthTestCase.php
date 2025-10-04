<?php

namespace Spatie\LaravelPasskeys\Tests;

use Spatie\LaravelPasskeys\Tests\TestSupport\Models\Admin;
use Spatie\LaravelPasskeys\Tests\TestSupport\Models\User;

class MultiAuthTestCase extends TestCase
{
    public function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        // Set deprecated configuration to null
        config()->set('passkeys.models.authenticatable');
        config()->set('passkeys.redirect_to_after_login');
        config()->set('passkeys.models.passkey');

        // Set configuration for multi auth passkeys
        config()->set('auth.providers.admins.driver', 'eloquent');
        config()->set('auth.providers.admins.model', Admin::class);
        config()->set('auth.guards.admin.driver', 'session');
        config()->set('auth.guards.admin.provider', 'admins');
        config()->set('passkeys.guards.web.authenticatable', User::class);
        config()->set('passkeys.guards.web.redirect_to_after_login', '/dashboard');
        config()->set('passkeys.guards.admin.authenticatable', Admin::class);
        config()->set('passkeys.guards.admin.redirect_to_after_login', '/admin/dashboard');

        $migration = include __DIR__.'/TestSupport/database/migrations/create_admins_table.php';
        $migration->up();

        $migration = include __DIR__.'/../database/migrations/update_passkeys_for_multi_auth.php.stub';
        $migration->up();
    }
}
