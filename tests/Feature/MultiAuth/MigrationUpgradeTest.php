<?php

use Illuminate\Support\Facades\Schema;
use Spatie\LaravelPasskeys\Models\Passkey;
use Spatie\LaravelPasskeys\Tests\TestSupport\Models\User;

it ('can upgrade passkeys table while keeping existing records healthy', function () {
    $passkey = Passkey::factory()->create();

    $this->assertDatabaseCount('passkeys', 1);
    $this->assertDatabaseCount('users', 1);

    $foreignKeysBefore = Schema::getForeignKeys('passkeys');

    expect($foreignKeysBefore)
        ->toBeArray()
        ->and(collect($foreignKeysBefore)->contains('columns', ['authenticatable_id']))
        ->toBeTrue('The original foreign key should exist before the migration.');

    $migration = include __DIR__.'/../../../database/migrations/update_passkeys_for_multi_auth.php.stub';
    $migration->up();

    $this->assertDatabaseHas('passkeys', [
        'authenticatable_type' => User::class,
        'authenticatable_id' => $passkey->authenticatable_id,
    ]);

    $foreignKeysAfter = Schema::getForeignKeys('passkeys');

    expect(collect($foreignKeysAfter)->contains('columns', ['authenticatable_id']))
        ->toBeFalse('The old foreign key should have been removed.')
        ->and(Schema::hasIndex('passkeys', 'passkeys_authenticatable_index'))
        ->toBeTrue('A new index on the polymorphic columns should exist.');
});
