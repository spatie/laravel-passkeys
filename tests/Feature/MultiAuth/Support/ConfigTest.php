<?php

use Spatie\LaravelPasskeys\Support\Config;
use Spatie\LaravelPasskeys\Tests\TestSupport\Models\Admin;
use Spatie\LaravelPasskeys\Tests\TestSupport\Models\User;

it ('can get model classes', function () {
    expect(Config::getAuthenticatableModel('web'))->not()->toBeNull();

    expect(Config::getAuthenticatableModel('admin'))->not()->toBeNull();
});

it ('can get correct model classes for specific auth guard', function () {
    expect(Config::getAuthenticatableModel('web'))->toBe(User::class);

    expect(Config::getAuthenticatableModel('admin'))->toBe(Admin::class);
});

it ('can get correct model with default guard', function () {
    expect(Config::getAuthenticatableModel())->toBe(User::class);
});
