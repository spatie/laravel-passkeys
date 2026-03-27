# Changelog

All notable changes to `laravel-passkeys` will be documented in this file.

## 1.6.5 - 2026-03-27

### What's Changed

* Fix PublicKeyCredentialSource type-hint issues with webauthn-lib 5.3+

In webauthn-lib 5.3+, several methods return `CredentialRecord` instead of `PublicKeyCredentialSource`, causing type errors in the Passkey model's `data` accessor and the factory.

## 1.6.4 - 2026-03-25

### What's Changed

* Allow phpdocumentor/reflection-docblock 6.x compatibility by using web-auth/webauthn-lib 5.3.x

Fixes #104

## 1.6.3 - 2026-02-27

Support Laravel 13

## 1.6.2 - 2026-02-25

Add Polish translations

## 1.6.1 - 2026-02-09

### What's Changed

* Support web-auth/webauthn-lib 5.3 (CredentialRecord) by @freekmurze in https://github.com/spatie/laravel-passkeys/pull/100

### New Contributors

* @freekmurze made their first contribution in https://github.com/spatie/laravel-passkeys/pull/100

**Full Changelog**: https://github.com/spatie/laravel-passkeys/compare/1.6.0...1.6.1

## 1.6.0 - 2026-02-09

### What's Changed

* Fixed: return resolved action class instead of null from `getActionClass()` by @ggiak in https://github.com/spatie/laravel-passkeys/pull/96
* Allow Livewire 3 and 4 in dev dependencies

### New Contributors

* @ggiak made their first contribution in https://github.com/spatie/laravel-passkeys/pull/96

**Full Changelog**: https://github.com/spatie/laravel-passkeys/compare/1.5.3...1.6.0

## 1.5.2 - 2025-12-11

### What's Changed

* Fixed: Deprecation warning from symfony/http-foundation 7.4 by @holdmyglass in https://github.com/spatie/laravel-passkeys/pull/89

### New Contributors

* @holdmyglass made their first contribution in https://github.com/spatie/laravel-passkeys/pull/89

**Full Changelog**: https://github.com/spatie/laravel-passkeys/compare/1.5.1...1.5.2

## 1.5.1 - 2025-11-27

### What's Changed

* Update Dutch translations for passkeys terminology by @Mantix in https://github.com/spatie/laravel-passkeys/pull/87
* Bump actions/checkout from 5 to 6 by @dependabot[bot] in https://github.com/spatie/laravel-passkeys/pull/86

**Full Changelog**: https://github.com/spatie/laravel-passkeys/compare/1.5.0...1.5.1

## 1.5.0 - 2025-10-28

### What's Changed

* Add configure ceremony step manager action by @gerpo in https://github.com/spatie/laravel-passkeys/pull/83
* Add remember parameter to authenticateWithPasskey by @gerpo in https://github.com/spatie/laravel-passkeys/pull/85

### New Contributors

* @gerpo made their first contribution in https://github.com/spatie/laravel-passkeys/pull/83

**Full Changelog**: https://github.com/spatie/laravel-passkeys/compare/1.4.1...1.5.0

## 1.4.1 - 2025-10-21

**Full Changelog**: https://github.com/spatie/laravel-passkeys/compare/1.4.0...1.4.1

## 1.4.0 - 2025-10-16

### What's Changed

* Remember me option added by @jegelkraut in https://github.com/spatie/laravel-passkeys/pull/80

**Full Changelog**: https://github.com/spatie/laravel-passkeys/compare/1.3.0...1.4.0

## 1.3.0 - 2025-10-15

### What's Changed

* Bump stefanzweifel/git-auto-commit-action from 6 to 7 by @dependabot[bot] in https://github.com/spatie/laravel-passkeys/pull/78
* Make allowedOrigins configurable by @jegelkraut in https://github.com/spatie/laravel-passkeys/pull/79

**Full Changelog**: https://github.com/spatie/laravel-passkeys/compare/1.2.0...1.3.0

## 1.2.0 - 2025-10-09

### What's Changed

* Skip null values added to serializer by @jegelkraut in https://github.com/spatie/laravel-passkeys/pull/77

### New Contributors

* @jegelkraut made their first contribution in https://github.com/spatie/laravel-passkeys/pull/77

**Full Changelog**: https://github.com/spatie/laravel-passkeys/compare/1.1.0...1.2.0

## 1.1.0 - 2025-09-25

### What's Changed

* Bump actions/checkout from 4 to 5 by @dependabot[bot] in https://github.com/spatie/laravel-passkeys/pull/71
* Update issue template by @AlexVanderbist in https://github.com/spatie/laravel-passkeys/pull/73
* Add authenticator selection criteria to GeneratePasskeyRegisterOptions by @DSpeichert in https://github.com/spatie/laravel-passkeys/pull/74

### New Contributors

* @AlexVanderbist made their first contribution in https://github.com/spatie/laravel-passkeys/pull/73
* @DSpeichert made their first contribution in https://github.com/spatie/laravel-passkeys/pull/74

**Full Changelog**: https://github.com/spatie/laravel-passkeys/compare/1.0.12...1.1.0

## 1.0.12 - 2025-07-28

### What's Changed

* Fix typo in passkeys array assignment in Inertia usage documentation. by @HichemTab-tech in https://github.com/spatie/laravel-passkeys/pull/62
* Bump aglipanci/laravel-pint-action from 2.5 to 2.6 by @dependabot[bot] in https://github.com/spatie/laravel-passkeys/pull/68
* Add Hungarian translations for passkeys by @Panyi in https://github.com/spatie/laravel-passkeys/pull/67

### New Contributors

* @HichemTab-tech made their first contribution in https://github.com/spatie/laravel-passkeys/pull/62
* @Panyi made their first contribution in https://github.com/spatie/laravel-passkeys/pull/67

**Full Changelog**: https://github.com/spatie/laravel-passkeys/compare/1.0.11...1.0.12

## 1.0.11 - 2025-06-23

### What's Changed

* add Simplified Chinese translations by @tanshiqi in https://github.com/spatie/laravel-passkeys/pull/60
* Bump stefanzweifel/git-auto-commit-action from 5 to 6 by @dependabot in https://github.com/spatie/laravel-passkeys/pull/56

### New Contributors

* @tanshiqi made their first contribution in https://github.com/spatie/laravel-passkeys/pull/60

**Full Changelog**: https://github.com/spatie/laravel-passkeys/compare/1.0.10...1.0.11

## 1.0.10 - 2025-06-19

### What's Changed

* Add Arabic translations for passkeys by @salahhusa9 in https://github.com/spatie/laravel-passkeys/pull/59

### New Contributors

* @salahhusa9 made their first contribution in https://github.com/spatie/laravel-passkeys/pull/59

**Full Changelog**: https://github.com/spatie/laravel-passkeys/compare/1.0.9...1.0.10

## 1.0.9 - 2025-06-10

### What's Changed

* Turkish translation of passkeys.php by @altayevrim in https://github.com/spatie/laravel-passkeys/pull/53

### New Contributors

* @altayevrim made their first contribution in https://github.com/spatie/laravel-passkeys/pull/53

**Full Changelog**: https://github.com/spatie/laravel-passkeys/compare/1.0.8...1.0.9

## 1.0.8 - 2025-05-20

### What's Changed

* Redirect to intended URL by @mralston in https://github.com/spatie/laravel-passkeys/pull/50

### New Contributors

* @mralston made their first contribution in https://github.com/spatie/laravel-passkeys/pull/50

**Full Changelog**: https://github.com/spatie/laravel-passkeys/compare/1.0.7...1.0.8

## 1.0.7 - 2025-05-20

### What's Changed

* Czech & Slovak translation by @hamrak in https://github.com/spatie/laravel-passkeys/pull/49

### New Contributors

* @hamrak made their first contribution in https://github.com/spatie/laravel-passkeys/pull/49

**Full Changelog**: https://github.com/spatie/laravel-passkeys/compare/1.0.6...1.0.7

## 1.0.6 - 2025-05-19

### What's Changed

* Better french translations by @sdebacker in https://github.com/spatie/laravel-passkeys/pull/48

**Full Changelog**: https://github.com/spatie/laravel-passkeys/compare/1.0.5...1.0.6

## 1.0.5 - 2025-05-19

### What's Changed

* Add French and Dutch translations for passkeys by @Mantix in https://github.com/spatie/laravel-passkeys/pull/47

### New Contributors

* @Mantix made their first contribution in https://github.com/spatie/laravel-passkeys/pull/47

**Full Changelog**: https://github.com/spatie/laravel-passkeys/compare/1.0.4...1.0.5

## 1.0.4 - 2025-05-18

### What's Changed

* add german translation by @NastyOOF in https://github.com/spatie/laravel-passkeys/pull/45

### New Contributors

* @NastyOOF made their first contribution in https://github.com/spatie/laravel-passkeys/pull/45

**Full Changelog**: https://github.com/spatie/laravel-passkeys/compare/1.0.3...1.0.4

## 1.0.3 - 2025-05-12

### What's Changed

* Create passkeys.php by @nessimabadi in https://github.com/spatie/laravel-passkeys/pull/40

### New Contributors

* @nessimabadi made their first contribution in https://github.com/spatie/laravel-passkeys/pull/40

**Full Changelog**: https://github.com/spatie/laravel-passkeys/compare/1.0.2...1.0.3

## 1.0.2 - 2025-05-12

### What's Changed

* startAuthentication refactor to expected call structure by @sdebacker in https://github.com/spatie/laravel-passkeys/pull/39

### New Contributors

* @sdebacker made their first contribution in https://github.com/spatie/laravel-passkeys/pull/39

**Full Changelog**: https://github.com/spatie/laravel-passkeys/compare/1.0.1...1.0.2

## 1.0.1 - 2025-05-12

### What's Changed

* remove duplicated fragment by @xHeaven in https://github.com/spatie/laravel-passkeys/pull/22
* Add config tests by @lrljoe in https://github.com/spatie/laravel-passkeys/pull/31
* Standardise on src/Support/Config rather than config() by @lrljoe in https://github.com/spatie/laravel-passkeys/pull/27
* Restore Laravel 11 Support by @lrljoe in https://github.com/spatie/laravel-passkeys/pull/28
* Bump dependabot/fetch-metadata from 2.3.0 to 2.4.0 by @dependabot in https://github.com/spatie/laravel-passkeys/pull/38
* Add Portuguese translations for passkey-related messages by @kidiatoliny in https://github.com/spatie/laravel-passkeys/pull/37
* [JS] startAuthentication refactor to expected call structure by @lrljoe in https://github.com/spatie/laravel-passkeys/pull/33

### New Contributors

* @xHeaven made their first contribution in https://github.com/spatie/laravel-passkeys/pull/22
* @kidiatoliny made their first contribution in https://github.com/spatie/laravel-passkeys/pull/37

**Full Changelog**: https://github.com/spatie/laravel-passkeys/compare/1.0.0...1.0.1

## 1.0.0 - 2025-05-06

**Full Changelog**: https://github.com/spatie/laravel-passkeys/compare/0.0.3...1.0.0

## 0.0.3 - 2025-05-06

**Full Changelog**: https://github.com/spatie/laravel-passkeys/compare/0.0.2...0.0.3

## 0.0.2 - 2025-05-06

**Full Changelog**: https://github.com/spatie/laravel-passkeys/compare/0.0.1...0.0.2

## 0.0.1 - 2025-05-05

- test release
