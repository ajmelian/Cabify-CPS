<?php

declare(strict_types=1);

namespace Cabify\Infrastructure\Config;

use RuntimeException;

/**
 * Nombre: EnvLoader
 * Descripción: Carga variables de entorno desde fichero .env para entorno local.
 * Parámetros de entrada: Ruta absoluta al fichero .env
 * Parámetros de salida: Variables disponibles por getenv
 * Método de uso: EnvLoader::load('/path/.env')
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class EnvLoader
{
    /**
     * Nombre: load
     * Descripción: Parsea un .env simple con formato CLAVE=VALOR.
     * Parámetros de entrada: string $envFilePath
     * Parámetros de salida: void
     * Método de uso: EnvLoader::load(__DIR__ . '/../../.env')
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public static function load(string $envFilePath): void
    {
        if (!is_file($envFilePath)) {
            return;
        }

        $lines = file($envFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            throw new RuntimeException('Unable to read .env file.');
        }

        foreach ($lines as $line) {
            $trimmedLine = trim($line);
            if ($trimmedLine === '' || str_starts_with($trimmedLine, '#')) {
                continue;
            }

            $parts = explode('=', $trimmedLine, 2);
            if (count($parts) !== 2) {
                continue;
            }

            $key = trim($parts[0]);
            $value = trim($parts[1]);

            if ($key === '' || getenv($key) !== false) {
                continue;
            }

            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}
