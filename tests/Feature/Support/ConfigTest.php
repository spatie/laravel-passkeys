<?php

use Spatie\LaravelPasskeys\Support\Config;

it('can get the model classes', function() {
   expect(Config::getPassKeyModel())->not()->toBeNull();

   expect(Config::getAuthenticatableModel())->not()->toBeNull();
});
