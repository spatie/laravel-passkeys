<?php

use Illuminate\Support\Facades\File;

it('loads the complete Czech translations without relying on the fallback locale', function (string $locale) {
    app()->setLocale($locale);
    app('translator')->setFallback('nl');

    $expected = [
        'authenticate_using_passkey' => 'Ověřit pomocí přístupového klíče',
        'create' => 'Vytvořit',
        'delete' => 'Smazat',
        'error_something_went_wrong_generating_the_passkey' => 'Při generování přístupového klíče došlo k chybě.',
        'invalid' => 'Nelze se přihlásit pomocí zadaného přístupového klíče',
        'last_used' => 'Naposledy použito',
        'name' => 'Název',
        'name_placeholder' => 'Zadejte název přístupového klíče',
        'no_passkeys_registered' => 'Nejsou registrovány žádné přístupové klíče',
        'not_used_yet' => 'Zatím nepoužito',
        'passkeys' => 'Přístupové klíče',
    ];

    expect(__('passkeys::passkeys'))->toBe($expected);

    foreach ($expected as $key => $translation) {
        expect(app('translator')->get("passkeys::passkeys.{$key}", [], null, false))->toBe($translation);
    }
})->with(['cs', 'cz']);

it('uses the configured package fallback and preserves missing keys', function (string $fallback, string $expected) {
    app()->setLocale('unknown');
    app('translator')->setFallback($fallback);

    expect(__('passkeys::passkeys.delete'))->toBe($expected)
        ->and(app('translator')->get('passkeys::passkeys.delete', [], null, false))->toBe('passkeys::passkeys.delete')
        ->and(__('passkeys::passkeys.missing'))->toBe('passkeys::passkeys.missing');
})->with([
    ['cs', 'Smazat'],
    ['cz', 'Smazat'],
    ['en', 'Delete'],
    ['nl', 'Verwijderen'],
]);

it('keeps application overrides independent for each Czech locale', function () {
    $path = sys_get_temp_dir().'/passkeys-translations-'.bin2hex(random_bytes(8));

    try {
        foreach (['cs' => 'Standard Czech override', 'cz' => 'Legacy Czech override'] as $locale => $translation) {
            File::ensureDirectoryExists("{$path}/vendor/passkeys/{$locale}");
            File::put("{$path}/vendor/passkeys/{$locale}/passkeys.php", '<?php return '.var_export(['create' => $translation], true).';');
        }

        app('translation.loader')->addPath($path);

        foreach (['cs' => 'Standard Czech override', 'cz' => 'Legacy Czech override'] as $locale => $translation) {
            app()->setLocale($locale);

            expect(__('passkeys::passkeys.create'))->toBe($translation)
                ->and(__('passkeys::passkeys.delete'))->toBe('Smazat');
        }
    } finally {
        File::deleteDirectory($path);
    }
});
