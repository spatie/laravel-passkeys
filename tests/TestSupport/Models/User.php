<?php

namespace Spatie\LaravelPasskeys\Tests\TestSupport\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\LaravelPasskeys\Models\Concerns\HasPasskeys;
use Spatie\LaravelPasskeys\Models\Concerns\InteractsWithPasskeys;
use Spatie\LaravelPasskeys\Tests\TestSupport\Factories\UserFactory;

class User extends \Illuminate\Foundation\Auth\User implements HasPasskeys
{
    use HasFactory;
    use InteractsWithPasskeys;

    protected static function newFactory(): Factory
    {
        return UserFactory::new();
    }
}
