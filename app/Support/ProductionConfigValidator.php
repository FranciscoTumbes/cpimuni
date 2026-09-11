<?php

namespace App\Support;

use Illuminate\Support\Facades\Config;
use RuntimeException;

class ProductionConfigValidator
{
    public static function validate(): void
    {
        if (app()->environment() !== 'production') {
            return;
        }

        $errors = [];

        if (Config::get('app.debug')) {
            $errors[] = 'APP_DEBUG debe estar desactivado en producción.';
        }

        $key = (string) Config::get('app.key');
        if ($key === '' || $key === 'base64:') {
            $errors[] = 'APP_KEY debe estar configurada en producción.';
        }

        if (! str_starts_with((string) Config::get('app.url'), 'https://')) {
            $errors[] = 'APP_URL debe usar HTTPS en producción.';
        }

        if (! Config::get('session.secure')) {
            $errors[] = 'SESSION_SECURE_COOKIE debe estar habilitado en producción.';
        }

        if (Config::get('filesystems.default') === 'public') {
            $errors[] = 'FILESYSTEM_DISK no puede ser public en producción.';
        }

        if ($errors !== []) {
            throw new RuntimeException('Configuración productiva insegura: '.implode(' ', $errors));
        }
    }
}
