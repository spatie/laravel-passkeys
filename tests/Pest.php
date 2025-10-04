<?php

use Spatie\LaravelPasskeys\Tests\MultiAuthTestCase;
use Spatie\LaravelPasskeys\Tests\TestCase;

uses(TestCase::class)->in(
    __DIR__ . '/Feature/Actions',
    __DIR__ . '/Feature/Livewire',
    __DIR__ . '/Feature/Support',
    __DIR__ . '/Feature/MultiAuth/MigrationUpgradeTest.php'
);

uses(MultiAuthTestCase::class)->in(
    __DIR__.'/Feature/MultiAuth/Livewire',
    __DIR__.'/Feature/MultiAuth/Support'
);
